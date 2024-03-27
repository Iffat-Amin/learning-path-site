
let $ = id => document.getElementById(id); 
let $all = query => document.querySelectorAll(query);
// for Login Page
function validateLogin() {
    let email = $('txt-login-email'),
        pass = $('txt-login-pass'),
        btnSubmit =  $('btn-login-submit'),
        errorLabel = $('lbl-login-emailValidations');
    btnSubmit.addEventListener('click', e => {
        if (email.value == "" || pass.value == "") {
            e.preventDefault();
            errorLabel.innerHTML = "Must fill all fields";
            errorLabel.style.visibility = "visible";
        }
        else if (!email.value.match(/[a-zA-Z0-9]*@[a-z]*\.com/i)) {
            e.preventDefault();
            errorLabel.innerHTML = "Invalid Email";
            errorLabel.style.visibility = "visible";
        }
    })
}

