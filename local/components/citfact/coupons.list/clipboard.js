function CopyToClipboard(containerid) {
    var copytext = document.createElement('input');
    copytext.value = containerid;
    document.body.appendChild(copytext);
    copytext.select();
    document.execCommand('copy');
    $('#container'+containerid+' span.promo-tooltip').html("Код купона скопирован: "+copytext.value);
    document.body.removeChild(copytext);    
    $('#container'+containerid+' span.promo-tooltip').show().delay(800).hide(2000);
}