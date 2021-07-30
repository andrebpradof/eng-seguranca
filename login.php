<script type="text/javascript">
	<?php 
		if ($_SESSION["loggedin"]=="True") {
			echo "var l_loggedIn = true;" . PHP_EOL;
		}else {
			echo "var l_loggedIn = false;" . PHP_EOL;
		}// end if
		if (isset($lAuthenticationAttemptResult)){
			echo "var lAuthenticationAttemptResultFlag = {$lAuthenticationAttemptResult};" . PHP_EOL;
		}else{
			echo "var lAuthenticationAttemptResultFlag = -1;".PHP_EOL;
		}// end if



		if (isset($_SESSION["tentativas"])){
			echo "var tentativas = {$_SESSION["tentativas"]};" . PHP_EOL;
		}else{
			echo "var tentativas = 0;".PHP_EOL;
		}// end if
	?>

	function onSubmitOfLoginForm(/*HTMLFormElement*/ theForm){
		try{
			var lUnsafeCharacters = /[`~!@#$%^&*()-_=+\[\]{}\\|;':",./<>?]/;
			if (theForm.username.value.length > 15 || 
				theForm.password.value.length > 15){
					alert('Nome de usuário muito longo.');
					return false;
			};// end if
				
			if (theForm.username.value.search(lUnsafeCharacters) > -1 || 
				theForm.password.value.search(lUnsafeCharacters) > -1){
					alert('Caracteres perigosos detectados. Não podemos permitir isso. Esta poderosa lista negra impedirá tais tentativas.');
					return false;
			};// end if
			
			return true;
		}catch(e){
			alert("Error: " + e.message);
		};// end catch
	};
</script>

<div class="page-title">Login</div>

<?php include_once (__ROOT__.'/includes/back-button.inc');?>
<?php include_once (__ROOT__.'/includes/hints/hints-menu-wrapper.inc'); ?>

<div id="id-log-in-form-div" style="display: tabble; text-align:center;">
	<form 	action="index.php?page=login.php"
			method="post" 
			enctype="application/x-www-form-urlencoded" 
			onsubmit="return onSubmitOfLoginForm(this);"
			id="idLoginForm">
		<table>
			<tr id="id-authentication-failed-tr" style="display: none;">
				<td id="id-authentication-failed-td" colspan="2" class="error-message"></td>
			</tr>
			<tr><td></td></tr>
			<tr>
				<td colspan="2" class="form-header">Please sign-in</td>
			</tr>
			<tr><td></td></tr>
			<tr>
				<td class="label">Username</td>
				<td>
					<input	type="text" name="username" size="20"
						autofocus="autofocus"
						minlength="1" maxlength="15" required="required"
					/>
				</td>
			</tr>
			<tr>
				<td class="label">Password</td>
				<td>
					<input type="password" name="password" size="20"
						minlength="1" maxlength="15" required="required"
					/>
				</td>
			</tr>

			<tr>
				<td class="label">
					<img src="captcha.php?l=150&a=50&tf=20&ql=5">
				</td>
				<td>
					<input type="text" name="palavra"/>
				</td>
			</tr>

			<tr><td></td></tr>
			<tr>
				<td colspan="2" style="text-align:center;">
					<input name="login-php-submit-button" class="button" type="submit" value="Login" />
				</td>
			</tr>
			<tr><td></td></tr>
			<tr>
				<td colspan="2" style="text-align:center; font-style: italic;">
					Dont have an account? <a href="index.php?page=register.php">Please register here</a>
				</td>
			</tr>
		</table>
	</form>
</div>

<div id="id-log-out-div" style="text-align: center; display: none;">
	<table>
		<tr>
			<td colspan="2" class="hint-header">You are logged in as <?php echo $_SESSION['logged_in_user']; ?></td>
		</tr>
		<tr><td></td></tr>
		<tr><td></td></tr>
		<tr>
			<td colspan="2" style="text-align:center;">
				<input class="button" type="button" value="Logout" onclick="document.location='index.php?do=logout'" />
			</td>
		</tr>
	</table>	
</div>

<script type="text/javascript">

	console.log(tentativas);

	var cUNSURE = -1;
   	var cACCOUNT_DOES_NOT_EXIST = 0;
   	var cPASSWORD_INCORRECT = 1;
   	var cNO_RESULTS_FOUND = 2;
   	var cAUTHENTICATION_SUCCESSFUL = 3;
   	var cAUTHENTICATION_EXCEPTION_OCCURED = 4;
   	var cUSERNAME_OR_PASSWORD_INCORRECT = 5;
   	
   	var lMessage = "";
   	var lAuthenticationFailed = "FALSE";
   	
	switch(lAuthenticationAttemptResultFlag){
   		case cACCOUNT_DOES_NOT_EXIST: 
   	   		lMessage="Account does not exist"; lAuthenticationFailed = "TRUE";
   	   		break;
   		case cPASSWORD_INCORRECT: 
   	   		lMessage="Password incorrect"; lAuthenticationFailed = "TRUE"; 
   	   		break;
   		case cNO_RESULTS_FOUND: 
   	   		lMessage="No results found"; lAuthenticationFailed = "TRUE"; 
   	   		break;
   		case cAUTHENTICATION_EXCEPTION_OCCURED: 
   	   		lMessage="Exception occurred"; lAuthenticationFailed = "TRUE"; 
   		break;
   		case cUSERNAME_OR_PASSWORD_INCORRECT: 
   	   		lMessage="Username or password incorrect"; lAuthenticationFailed = "TRUE"; 
   		break;
   	};

	if(lAuthenticationFailed=="TRUE"){
		document.getElementById("id-authentication-failed-tr").style.display="";
		document.getElementById("id-authentication-failed-td").innerHTML=lMessage;
	}

	// if (!l_loggedIn){
	// 	document.getElementById("id-log-in-form-div").style.display="";
	// 	document.getElementById("id-log-out-div").style.display="none";
	// }else{
	// 	document.getElementById("id-log-in-form-div").style.display="none";
	// 	document.getElementById("id-log-out-div").style.display="";		
	// }	
</script>