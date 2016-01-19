<html>
	<head>
	<meta charset="utf-8">
	<script src="http://libs.baidu.com/jquery/1.8.0/jquery.min.js"></script>
	</head>
	<body>
		<a onclick="test()" class="stone">检查傻逼</a>
		<script>
			function test(id)
			{
				$.ajax({
					url:'/index.php?act=ajax.test',
					dataType: 'json',
					type: 'GET',
					success: function(result)
					{
						alert(result.sbmj);
					},
				})
			};
		</script>
	</body>
</html>