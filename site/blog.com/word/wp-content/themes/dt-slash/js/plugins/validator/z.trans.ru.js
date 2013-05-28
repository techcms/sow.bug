$.validationEngine.allRules = {
               "required":{ 
						"regex":"none",
						"alertText":"* Пожалуйта, заполните это поле",
  						"alertTextTitle":"* Пожалуйта, укажите ",
						"alertTextCheckboxMultiple":"* Please select an option",
						"alertTextCheckboxe":"* This checkbox is required"},
					"or": {
					   "regex":"none",
					   "alertText":"Заполните одно из этих полей:",
					   "alertText2":"* ",
					   "alertText3":"или:",
					   "alertTextTitle":"* Укажите "
					},
					"length":{
						"regex":"none",
						"alertText":"* Необходимо ввести от",
						"alertText2":" до ",
						"alertText3": " символов"},
					"maxCheckbox":{
						"regex":"none",
						"alertText":"* Checks allowed Exceeded"},	
					"minCheckbox":{
						"regex":"none",
						"alertText":"* Пожалуйста, выберите ",
						"alertText2":" вариант"},	
					"confirm":{
						"regex":"none",
						"alertText":"* Введенные пароли не совпадают"},		
					"telephone":{
						"regex":"/^[0-9\-\(\)\ ]+$/",
						"alertText":"* Invalid phone number"},	
					"email":{
						"regex":"/^[a-zA-Z0-9_\.\-]+\@([a-zA-Z0-9\-]+\.)+[a-zA-Z0-9]{2,4}$/",
						"alertText":"* Адрес e-mail введен неверно"},	
					"date":{
                         "regex":"/^[0-9]{4}\-\[0-9]{1,2}\-\[0-9]{1,2}$/",
                         "alertText":"* Invalid date, must be in YYYY-MM-DD format"},
					"onlyNumber":{
						"regex":"/^[0-9\ ]+$/",
						"alertText":"* Numbers only"},	
					"noSpecialCaracters":{
						"regex":"/^[0-9a-zA-Z]+$/",
						"alertText":"* No special caracters allowed"},	
					"ajaxUser":{
						"file":"validateUser.php",
						"alertTextOk":"* This user is available",	
						"alertTextLoad":"* Loading, please wait",
						"alertText":"* This user is already taken"},	
					"ajaxName":{
						"file":"validateUser.php",
						"alertText":"* This name is already taken",
						"alertTextOk":"* This name is available",	
						"alertTextLoad":"* Loading, please wait"},		
					"onlyLetter":{
						"regex":"/^[a-zA-Z\ \']+$/",
						"alertText":"* Letters only"}
					}	
