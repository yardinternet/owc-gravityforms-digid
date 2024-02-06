<div class='modal fade owc-gf-digid-hidden' id='modalWrapperDigiD' tabindex='-1' role='dialog' aria-labelledby='modalWrapperDigiD' aria-modal='true' aria-hidden='true'>
	<div id='modalDialogDigiD' class='modal-dialog' role='document'>
		<div class='modal-content'>
			<div class='modal-header'>
				<h5 class='modal-title' id='exampleModalLabel'>Uw sessie verloopt.</h5>
			</div>
			<div class='modal-body | mb-4'>
				Uw sessie is mogelijk verlopen. Als u te lang niks hebt gedaan, wordt u uit veiligheidsoverwegingen door DigiD uitgelogd.
				Kies 'Verlengen' om uw sessie te verlengen, mogelijk moet u opnieuw inloggen met DigiD.
			</div>
			<div class='modal-footer | d-flex justify-content-end' >
				<form action="{{ logoutLink }}" method="dialog">
					<button type='submit' id='js-abortSession-DigiD' tabindex='0' role='button' class='btn btn-outline-primary mr-2' data-dismiss='modal'>Sluiten</button>
				</form>
				<button type='button' id='js-resumeSession-DigiD' tabindex='0' role='button' class='btn btn-primary'>Verlengen</button>
			</div>
		</div>
	</div>
</div>
