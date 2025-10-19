<!doctype html>
<html lang="pt-BR">
<head>
  <meta charset="utf-8">
  <title>Preview automático</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
  <script src="assets/js/script_images.js"></script> 
</head>
<body class="p-4">
	<input type="file" class="script_images" accept="image/*" data-base64="" data-mime="">
	<img id="testeImg" src="">
	
<script type="text/javascript">
	$(".script_images").on("base64-ready", function (e, data) {
		$("#testeImg").attr("src", data.base64);
		console.log(  data.base64);
		console.log( (data.mime).replace("/", ".") );
		$.ajax({
            type: "POST",
            url: "Base64BackEnd.php",
            data: { imgData: data.base64, imgMime: data.mime },
            success: function(response) {
                console.log("Imagem enviada com sucesso!");
                console.log("Resposta do servidor:", response);
            },
            error: function(xhr, status, error) {
                console.error("Erro ao enviar a imagem:", error);
            }
        });
	});
</script>
</body>
</html>
