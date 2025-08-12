const form = document.getElementById("upload-form");
const submitButton = document.getElementById("submit-button");

function disableButton() {
  submitButton.disabled = true;
  submitButton.querySelector(":not(.loading)").classList.add("hidden");
  submitButton.querySelector(".loading").classList.remove("hidden");
}

function resetButton() {
  submitButton.disabled = false;
  submitButton.querySelector(":not(.loading)").classList.remove("hidden");
  submitButton.querySelector(".loading").classList.add("hidden");
}

const uploadFile = async () => {
  const formData = new FormData(form);
  const fileStem = formData
    .get("fasta")
    .name.replace(/^.*[\\/]/, "")
    .replace(/\.[^/.]+$/, "");
  const bedFile = `${fileStem}.bed`;

  disableButton();
  const response = await fetch(form.action, {
    method: form.method,
    body: formData,
  });
  if (!response.ok) {
    message = await response.text();
    showToast(message, `Please check the input and try again.`, true);
    dispatchEvent(
      new CustomEvent("analysis-failed", {
        detail: { message: message },
        bubbles: true,
      })
    );
    resetButton();
    return;
  }

  const data = await response.json();
  dispatchEvent(
    new CustomEvent("analysis-finished", {
      detail: {
        amplicons: data.amplicons,
        bed: data.bed,
        bedFile: bedFile,
        chromosome: data.chromosome,
      },
      bubbles: true,
    })
  );

  showToast(
    "Analysis finished!",
    `${bedFile} was downloaded. Please check your Downloads folder.`
  );

  form.reset();
  resetButton();
};

form.addEventListener("submit", function (event) {
  event.preventDefault();
  uploadFile();
});
