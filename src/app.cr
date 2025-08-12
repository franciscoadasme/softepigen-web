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
  kmer = env.params.body["astringent"]? ? 5 : 4

  begin
    Log.info { "Processing file #{file_upload.filename}..." }
    file_upload.tempfile.rewind
    amplicons = Softepigen.find_amplicons file_upload.tempfile, primer_size, amplicon_size, allowed_cpg, kmer
amplicons = Softepigen.fold_amplicons(amplicons)
    file_upload.tempfile.rewind
    chromosome = file_upload.tempfile.read_line.lchop('>').split(/[\-:]/)[0]
  rescue err : Exception
    Log.warn exception: err
    halt env, 500, "Error processing file"
  end
  bedfile = File.tempfile ".bed"
  Softepigen.write_bed bedfile, chromosome, amplicons

  filename = "#{Path[file_upload.filename || "output"].stem}.bed"
  env.response.headers["Content-Disposition"] = %{attachment; filename="#{filename}"}
  send_file env, bedfile.path, "text/plain"
end

Kemal.run
