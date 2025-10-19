
$(document).ready(function () {
  $(".file2Base64").on("change", function () {
    const input = $(this);
    const file = this.files[0];

    if (file) {
      const reader = new FileReader();
      reader.onload = function (e) {
        const base64 = e.target.result;
        input.attr("data-base64", base64);
        input.attr("data-mime", file.type);
        input.trigger("base64-ready", { base64, mime: file.type });
      };
      reader.readAsDataURL(file);
    }
  });
});
