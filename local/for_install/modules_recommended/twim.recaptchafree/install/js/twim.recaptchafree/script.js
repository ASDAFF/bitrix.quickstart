/* 
	ReCaptcha 2.0 Google js
	modul bitrix
	Shevtcoff S.V. 
	date 24.03.17
	time 08:55
	
	Recaptchafree - object
	Recaptchafree.render() - method render
	Recaptchafree.reset() - method reset
	Recaptchafree.items - array id widget
*/

/* global grecaptcha */

"use strict";

var Recaptchafree = Recaptchafree || {};
Recaptchafree.items = new Array();
Recaptchafree.form_submit;
/**
 * render recaptcha
 * @returns false
 */
Recaptchafree.render = function() { 
   if(window.grecaptcha){
        var elements = document.querySelectorAll('div.g-recaptcha');
        var widget;
        Recaptchafree.items = [];
        for (var i = 0; i < elements.length; i++) {
            if(elements[i].innerHTML === "") {
                widget = grecaptcha.render(elements[i], {
                    'sitekey' : elements[i].getAttribute("data-sitekey"),
                    'theme' : elements[i].getAttribute("data-theme"),
                    'size' : elements[i].getAttribute("data-size"),
                    'callback' : elements[i].getAttribute("data-callback"),
                    'badge' : elements[i].getAttribute("data-badge")
                });
                elements[i].setAttribute("data-widget", widget);
                Recaptchafree.items.push(widget);
            } else {
                widget =  elements[i].getAttribute("data-widget");
                Recaptchafree.items.push(parseInt(widget));
            }
        }
    } 
    
};
/**
 * reset recaptcha after ajax or show modal
 * @returns  false
 */
Recaptchafree.reset = function() { 
   if(window.grecaptcha){
        Recaptchafree.render();
            for (var i = 0; i < Recaptchafree.items.length; i++) {
                grecaptcha.reset(Recaptchafree.items[i]);
            } 

    }  
};
/**
 * callback submit form with invisible recaptcha
 * @param {type} token
 * @returns false
 */
function RecaptchafreeSubmitForm(token) {
    if(Recaptchafree.form_submit !== undefined){
        var x = document.createElement("INPUT"); // create token input
        x.setAttribute("type", "hidden");  
        x.name = "g-recaptcha-response";
        x.value = token;
        Recaptchafree.form_submit.appendChild(x);  // append current form
        var elements = Recaptchafree.form_submit.elements;
        for (var i = 0; i < elements.length; i++) {
            if(elements[i].getAttribute("type") === "submit")  {
                var submit_hidden = document.createElement("INPUT"); // create submit input hidden
                submit_hidden.setAttribute("type", "hidden");  
                submit_hidden.name = elements[i].name;
                submit_hidden.value = elements[i].value;
                Recaptchafree.form_submit.appendChild(submit_hidden);  // append current form
            }
        }
        document.createElement('form').submit.call(Recaptchafree.form_submit); // submit form
    }       
};
/**
 * onload recaptcha  
 * @returns  false
 */
function onloadRecaptchafree(){
    Recaptchafree.render();
    // If invisible recaptcha on the page
    if (document.addEventListener) { 
        document.addEventListener('submit',function(e){
            if(e.target && e.target.tagName === "FORM"){
                var g_recaptcha = e.target.querySelectorAll('div.g-recaptcha');
                if(g_recaptcha[0] !== undefined && g_recaptcha[0].getAttribute("data-size") === "invisible"){
                    var widget_id = g_recaptcha[0].getAttribute("data-widget");
                    grecaptcha.execute(widget_id);
                    Recaptchafree.form_submit = e.target;
                    e.preventDefault();    
                }
            } 
        }, false);
    } else {
        document.attachEvent("onsubmit", function(e){
            var target = e.target || e.srcElement;
            if(e.target && e.target.tagName === "FORM"){
                var widget_id = e.target.getAttribute("data-widget");
                grecaptcha.execute(widget_id);
                Recaptchafree.form_submit = target;    
                e.returnValue = false;
            }
        });
    }
    // hide grecaptcha-badge, if multi invisible recaptcha
    var badges = document.querySelectorAll('.grecaptcha-badge'); 
    for (var i = 1; i < badges.length; i++) {
        badges[i].style.display="none";
    }
} 