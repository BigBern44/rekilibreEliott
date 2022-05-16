
$(document).ready(function () {
    var nbPayment = 1;
    $('#addpayment').click(function () {
        var div = document.createElement('div');
        div.innerHTML = this.getAttribute('data-prototype').split('__name__').join(nbPayment);
        document.getElementById('layout-payments').appendChild(div);
        nbPayment++;
        $('select').selectpicker({
            title: 'Aucune sélection'
        });
    });

    $('.addpayment').click(function () {
        var div = document.createElement('div');
        div.innerHTML = this.getAttribute('data-prototype').split('__name__').join(nbPayment);
        console.log('layout-payments'+this.getAttribute('idDiv'));
        document.getElementById('layout-payments'+this.getAttribute('idDiv')).appendChild(div);
        nbPayment++;
        $('select').selectpicker({
            title: 'Aucune sélection'
        });
    });
});