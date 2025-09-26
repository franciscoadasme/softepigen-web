require "kemal"
require "softepigen"

FILE_LIFETIME = 1.hour

def delete_old_files
  Dir.glob("public/output/*").each do |path|
    if Time.local - File.info(path).modification_time > FILE_LIFETIME
      Log.info { "Deleting old file #{path}" }
      File.delete? path # to avoid exception if file was already deleted
    end
  end
end

before_get do
  delete_old_files
end

get "/" do
  render "src/views/index.ecr", "src/views/layout.ecr"
end

get "/download/:slug/bed" do |env|
  slug = env.params.url["slug"]? || halt(env, 400, "Missing slug")
  bed_path = Path["public/output/#{slug}-out.bed"]
  halt(env, 404, "File not found") unless File.exists?(bed_path)
  env.response.headers["Content-Type"] = "application/octet-stream"
  env.response.headers["Content-Disposition"] = "attachment; filename=\"#{slug}-out.bed\""
  contents = File.read(bed_path)
  File.delete bed_path
  contents
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
    slug = "#{timestamp}-#{stem}"
    output = "public/output/#{slug}"
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
      amplicons = io.to_s[/Found (\d+) amplicon/, 1]?.try(&.to_i) || 0
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
    "chromosome" => "chr??",
    "slug"       => slug,
  }.to_json
end

Kemal.run
