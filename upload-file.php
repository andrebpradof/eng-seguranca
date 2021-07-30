<?php include_once(__ROOT__ . '/classes/FileUploadExceptionHandler.php'); ?>
<?php include_once(__ROOT__ . '/includes/back-button.inc'); ?>
<?php include_once(__ROOT__ . '/includes/hints/hints-menu-wrapper.inc'); ?>
<?php
try {

	$lAllowedFileSize = 20000;
	$lUploadDirectoryFlag = "TEMP_DIRECTORY";

	//$lWebServerUploadDirectory = __ROOT__.DIRECTORY_SEPARATOR.'uploads';
	$lWebServerUploadDirectory = sys_get_temp_dir();
	$lFormSubmitted = $lFileMovedSuccessfully = FALSE;
	if (isset($_POST["upload-file-php-submit-button"]) || isset($_REQUEST["upload-file-php-submit-button"])) {
		$lFormSubmitted = TRUE;
	} // end if

	if ($lFormSubmitted) {

		switch ($lUploadDirectoryFlag) {
			case "CLIENT_DECIDES":
				$lTempDirectory = $_REQUEST["UPLOAD_DIRECTORY"];
				break;
			case "WEB_SERVER":
				$lTempDirectory = $lWebServerUploadDirectory;
				break;
			case "TEMP_DIRECTORY":
				$lTempDirectory = sys_get_temp_dir();
				break;
		} // end switch

		/* Common file properties */
		$lFilename = $_FILES["filename"]["name"];
		$lFileTempName = $_FILES["filename"]["tmp_name"];
		$lFileType = $_FILES["filename"]["type"];
		$lFileSize = $_FILES["filename"]["size"];
		$lFileUploadErrorCode = $_FILES["filename"]["error"];
		$lFilePermanentName = $lTempDirectory . DIRECTORY_SEPARATOR . $lFilename;

		/* File properties needed for validation */
		$lAllowedFileExtensions = array("gif", "jpeg", "jpg", "png");
		$lAllowedFileTypes = array("image/gif", "image/jpeg", "image/jpg", "image/pjpeg", "image/x-png", "image/png");
		$lFilenameParts = explode(".", $lFilename);
		$lFileExtension = end($lFilenameParts);
		$lValidationMessage = "Validação não realizada";
		$lFileMovedMessage = "Não houve tentativa de mover o arquivo";

		/* File property strings suitible for printing */
		if ($lFileSize > 1000) {
			$lFileSizeString = number_format($lFileSize / 1000) . " KB";
		} else {
			$lFileSizeString = number_format($lFileSize) . " Bytes";
		} //end if

		if ($lAllowedFileSize > 1000) {
			$lAllowedFileSizeString = number_format($lAllowedFileSize / 1000) . " KB";
		} else {
			$lAllowedFileSizeString = number_format($lAllowedFileSize) . " Bytes";
		} //end if

		$lFileUploadMessage = "File uploaded to {$lFileTempName}";
		if ($lFileUploadErrorCode != UPLOAD_ERR_OK) {
			$lFileUploadMessage = "Error detected during file upload (Code {$lFileUploadErrorCode}). See error output for detail.";
			throw new FileUploadExceptionHandler($lFileUploadErrorCode);
		} //end if UPLOAD_ERR_OK

		$lFileValid = TRUE;

		$lValidationMessage = "Validação realizada.";

		if (!in_array($lFileExtension, $lAllowedFileExtensions)) {
			$lValidationMessage .= " Extensão de arquivo {$lFileExtension} não permitida.";
			$lFileValid = FALSE;
		} // end if

		if (!in_array($lFileType, $lAllowedFileTypes)) {
			$lValidationMessage .= " Tipo de arquivo {$lFileType} não permitido.";
			$lFileValid = FALSE;
		} // end if

		if ($lFileSize > $lAllowedFileSize) {
			$lValidationMessage .= "Tamanho do arquivo {$lFileSizeString} excede o tamanho de arquivo permitido {$lAllowedFileSizeString}.";
			$lFileValid = FALSE;
		} // end if


		if ($lFileValid) {
			if (move_uploaded_file($lFileTempName, $lFilePermanentName)) {
				$lFileMovedSuccessfully = TRUE;
				$lFileMovedMessage = "Arquivo movido para {$lFilePermanentName}";
			} else {
				$lFileMovedSuccessfully = FALSE;
				$lFileMovedMessage = "Erro detectado. Não foi possível mover o arquivo temporário PHP {$lTempDirectory} para um local permanente {$lFilePermanentName}";
				throw new Exception($lFileMovedMessage);
			} //end if move_uploaded_file
		} // end if $lFileValid

	} //end if $lFormSubmitted
} catch (Exception $e) {
	echo $CustomErrorHandler->FormatError($e, "Error uploading file");
} // end try	
?>

<script type="text/javascript">
	var onSubmitOfForm = function( /* HTMLForm */ theForm) {

		try {
			var lMAX_FILE_SIZE = <?php echo $lAllowedFileSize; ?>;

			if (theForm.id_max_file_size.value > lMAX_FILE_SIZE) {
				alert('O tamanho máximo do arquivo não pode ser maior que ' + lMAX_FILE_SIZE);
				return false;
			}; // end if

			return true;
		} catch (e) {
			alert("Error: " + e.message);
		}; // end catch

	}; // end JavaScript function onSubmitOfForm()
</script>

<div class="page-title">Upload a File</div>
<div>&nbsp;</div>

<?php
if ($lFormSubmitted) {
	echo "<div>
				<table style='width: 600px;'>
					<tr><td class='label' colspan='2'>{$lFileUploadMessage}</td></tr>
					<tr><td class='label' colspan='2'>{$lFileMovedMessage}</td></tr>
					<tr><td class='label' colspan='2'>{$lValidationMessage}</td></tr>
					<tr><td>&nbsp;</td></tr>
					<tr><td class='label'>Original File Name</td><td>{$lFilename}</td></tr>
					<tr><td class='label'>Temporary File Name</td><td>{$lFileTempName}</td></tr>
					<tr><td class='label'>Permanent File Name</td><td>{$lFilePermanentName}</td></tr>
					<tr><td class='label'>File Type</td><td>{$lFileType}</td></tr>
					<tr><td class='label'>File Size</td><td>{$lFileSizeString}</td></tr>
				</table>	
			</div>
			<div>&nbsp;</div>";
} //end if
?>

<div>
	<form enctype="multipart/form-data" action="./index.php?page=upload-file.php" method="POST" onsubmit="return onSubmitOfForm(this);">
		<table>
			<tr id="id-bad-cred-tr" style="display: none;">
				<td colspan="2" class="error-message">
					Authentication Error: File upload error
				</td>
			</tr>
			<tr>
				<td></td>
			</tr>
			<tr>
				<td colspan="2" class="form-header">Please choose file to upload</td>
			</tr>
			<tr>
				<td></td>
			</tr>
			<tr>
				<td colspan="2">
					<!-- MAX_FILE_SIZE must precede the file input field -->
					<input type="hidden" name="MAX_FILE_SIZE" id="id_max_file_size" value="<?php echo $lAllowedFileSize; ?>" />
					<label for="filename-text" class="label">Filename</label>
					<input type="text" style="background-color:#ffffff;color:#000000;font-family:courier" disabled="disabled" name="filename-text" id="idFilenameText" size="50" />
					<img src="./images/upload-32-32.png" align="middle" onclick="idFilename.click();" />
					<input type="file" id="idFilename" name="filename" style="display: none;" onchange="idFilenameText.value=this.value" />
				</td>
			</tr>
			<tr>
				<td></td>
			</tr>
			<tr>
				<td colspan="2" style="text-align:center;">
					<input name="upload-file-php-submit-button" class="button" type="submit" value="Upload File" />
				</td>
			</tr>
			<tr>
				<td></td>
			</tr>
		</table>
	</form>
</div>