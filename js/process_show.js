(function () {

    // getElementById
    function $id(id) {
        return document.getElementById(id);
    }

    function sleep(delay) {
        let start = (new Date()).getTime();
        while ((new Date()).getTime() - start < delay) {
            continue;
        }
    }

    let m = $id("progress"),i=0;

    window.onload= function () {
        m.innerText = "0";
        let interval=setInterval(function () {
            m.innerText = (parseInt( m.innerText ) +1).toString() ;
            i+=1;

            if (i>=100) {
                clearInterval(interval);
            }
        },1000);
    };



})();