function validate_password()
{
	if (document.regForm.password.value != document.regForm.password2.value)
	{
		jError('Please confirm your password!',
				{
					onClosed:function()
					{
						document.regForm.password.value = '';
						document.regForm.password2.value = '';
						document.regForm.password.focus();
					}
				}
			);
		
		return false;
	}
	return true;
}