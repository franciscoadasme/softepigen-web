require "kemal"
require "softepigen"

get "/" do
  render "src/views/index.ecr", "src/views/layout.ecr"
end

post "/upload" do |env|
  file_upload = env.params.files["fasta"]? || halt(env, 400, "No file uploaded")
  halt(env, 400, "Empty file") unless file_upload.filename.presence
  amplicon_size_min = env.params.body["amplicon_size_min"].to_i? ||
                      halt(env, 400, "Invalid amplicon size")
  amplicon_size_max = env.params.body["amplicon_size_max"].to_i? ||
                      halt(env, 400, "Invalid amplicon size")
  amplicon_size = amplicon_size_min..amplicon_size_max
  primer_size_min = env.params.body["primer_size_min"].to_i? ||
                    halt(env, 400, "Invalid primer size")
  primer_size_max = env.params.body["primer_size_max"].to_i? ||
                    halt(env, 400, "Invalid amplicon size")
  primer_size = primer_size_min..primer_size_max
  cpg_min = env.params.body["cpg_min"].to_i? || halt(env, 400, "Invalid cpg")
  cpg_max = env.params.body["cpg_max"].to_i? || halt(env, 400, "Invalid cpg")
  allowed_cpg = cpg_min..cpg_max
  astringent = env.params.body["astringent"]? ? 1 : 0

  begin
    Log.info { "Processing file #{file_upload.filename}..." }

    timestamp = Time.local.to_s("%Y%m%d%H%M%S")
    stem = File.basename Path[file_upload.filename.not_nil!].stem, ".fasta"
    output = "public/output/#{timestamp}-#{stem}"
    bed_file = "#{output}-out.bed"

    args = [
      "--amplicon=#{amplicon_size.to_s.sub("..", ",")}",
      "--primer=#{primer_size.to_s.sub("..", ",")}",
      "--cpg=#{allowed_cpg.to_s.sub("..", ",")}",
      "--astringency=#{astringent}",
      "--output=#{output}",
      file_upload.tempfile.path,
    ]
    io = IO::Memory.new
    status = Process.run "./bin/softepigen", args, output: io, error: io
    if status.success?
      bed_content = File.exists?(bed_file) ? File.read(bed_file) : ""
      chromosome = bed_content[/browser position (chr\d+)/, 1]? || "chr??"
      amplicons = `tail -1 #{bed_file}`[/Neg(\d+)/, 1]? || 0
    else
      message = io.to_s.strip
      Log.error { "Softepigen failed: #{message}" }
      halt env, 500, "Failed to process file due to #{message}"
    end
  rescue err : Exception
    Log.error exception: err
    halt env, 500, "Failed to process file due to #{err}"
  end

  env.response.headers["Content-Type"] = "application/json"
  {
    "amplicons"  => amplicons,
    "bed"        => bed_content,
    "chromosome" => chromosome,
  }.to_json
end

Kemal.run
