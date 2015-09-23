<html>
	<head>
		<title>
			<?php echo V3_HOST; ?> - Social Connect
		</title>
		<style type="text/css">
			p {
				margin: 5px 0;
				padding: 0;
			}
			p.description {

			}
			p.errors {
				color: #C00;
				font-weight: small;
			}
			input#username {
				width: 300px;
				padding: 5px 3px 3px 5px;
			}
			button {
				height: 27px;
				vertical-align: middle;
			}
		</style>
	</head>
	<body>
		<form method="POST">
			<div>
				<p class="description">
					Please enter <b><?php echo AUTH_SERVICE ?></b> username to continue!
				</p>
				<?php if(isset($_GET['msg'])) : ?>
				<p class="errors">
					Username is incorrect!. <?php echo $_GET['msg']; ?>.
				</p>
				<?php endif; ?>
			</div>
			<div>
				<p>
				<input type="text" name="username" id="username" value="<?php echo $username; ?>" autocomplete="off" />
				</p>
			</div>
			<div>
				<p>
				<button type="submit">
					Continue
				</button>
				<button type="button" onclick="self.close();">
					Cancel
				</button>
				</p>
			</div>
		</form>
	</body>
</html>