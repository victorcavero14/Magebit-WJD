var email = document.getElementById('email');
var checkbox = document.getElementById('checkbox');
var error = document.getElementById('error');
var emailRight = false

window.onload = function() {

    document.getElementById('form').onsubmit = function() {
        var subs = document.getElementById('subscribe');
        var subsInfo = document.getElementById('subscribe-info');
        var trophy = document.getElementById('trophy')

        if( /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) ) {
            subs.textContent = 'Thanks for subscribing!';
            subs.style.top = '139px'
    
            subsInfo.textContent = 'You have successfully subscribed to our email listing. Check your email for the discount code.'
            subsInfo.style.top = '183px'

            document.getElementById('input1').style.display = 'none'
            document.getElementById('input2').style.display = 'none'
    
            document.getElementById('trophy').style.display = 'inline-block'
            trophy.style.position = 'absolute';
            trophy.style.width = '44px';
            trophy.style.height = '70px';
            trophy.style.left = '40px';
            trophy.style.top = '284px';
            trophy.style.background = "url(img/ic_success.svg)"
        }
        else {
            subs.textContent = 'Thanks for subscribing!';
            subs.style.top = '346px'
    
            subsInfo.textContent = 'You have successfully subscribed to our email listing. Check your email for the discount code.'
    
            document.getElementById('input1').style.display = 'none'
            document.getElementById('input2').style.display = 'none'
            document.getElementById('separator').style.top = '539px'
            document.getElementById('social-media').style.top = '590px'
    
            document.getElementById('trophy').style.display = 'inline-block'
            trophy.style.position = 'absolute';
            trophy.style.width = '44px';
            trophy.style.height = '70px';
            trophy.style.left = '140px';
            trophy.style.top = '269px';
            trophy.style.background = "url(img/ic_success.svg)"
        }
        return true; // True if we want to the see the PHP with the Database or False if we do not want to.
    };
}


email.addEventListener('keyup', function checkErrors(){
        /*
            Possible errors:
                - Wrong email
                - Not marked checkbox
                - No email address provided
                - Email ending in .co 
        */
        
        if(!isEmail()) {
            error.textContent = 'Please provide a valid e-mail address';
            emailRight = false;
        }
        else if(emailIsFromColombia()){
            error.textContent =  'We are not accepting subscriptions from Colombia emails';
            emailRight = false;
        }
        else if(isEmpty(email.value)){
            console.log(email.value)
            emailRight = false;
            error.textContent= 'Email address is required';
        }
        else if(!checkbox.checked)
        {
            error.textContent = 'You must accept the terms and conditions';
            emailRight = true
        }
        else{
            error.textContent = '';
            document.getElementById('submit').disabled = false;
            emailRight = true
        }

    }
);


checkbox.addEventListener('change', 
    function parseCheckbox(){
        if(emailRight) {
            if(checkbox.checked) {
                document.getElementById('submit').disabled = false;
                error.textContent = ''
            }
            else {
                error.textContent = 'You must accept the terms and conditions';
                document.getElementById('submit').disabled = true;
            }
        }
        else {
            error.textContent = 'Please insert a valid email';
            document.getElementById('submit').disabled = true;
        }
    }
);

function isEmail() {
    return email.value.match(/([a-zA-Z0-9+._-]+@[a-zA-Z0-9._-]+\.[a-zA-Z0-9_-]+)/gi);
}

function emailIsFromColombia() {
    return (email.value.match(/([a-zA-Z0-9+._-]+@[a-zA-Z0-9._-]+\.co)/gi) && email.value.slice(-2) == 'co');
}

function isEmpty(str) {
    return !str.trim().length;
}


