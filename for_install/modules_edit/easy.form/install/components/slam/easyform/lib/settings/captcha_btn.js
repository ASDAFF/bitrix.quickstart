function JCCustomFormOpen(arParams) {
    this.jsOptions = JSON.parse(arParams.data);
    this.arParams = arParams;

    var obButton = document.createElement('INPUT');
    obButton.type = "button";
    obButton.value = this.jsOptions.BUTTON_NAME;
    this.arParams.oCont.appendChild(obButton);

    obButton.onclick = BX.delegate(JCCustomFormOpen.btnClick, this);
    this.saveData = BX.delegate(JCCustomFormOpen.__saveData, this);
}


JCCustomFormOpen.btnClick = function () {

    var strUrl = this.jsOptions.COMPONENT_PATH + '/lib/settings/captcha_btn.php'
        + '?lang=' + this.jsOptions.LANG;


    window.jsPopupCustomFormOpen = new BX.CDialog({
        'content_url': strUrl,
        'width': 800, 'height': 600,
        'resizable': false
    });

    window.jsPopupCustomFormOpen.Show();
    window.jsPopupCustomFormOpen.PARAMS.content_url = '';
    return false;
};

JCCustomFormOpen.__saveData = function () {
    window.jsPopupCustomFormOpen.Close();
};

