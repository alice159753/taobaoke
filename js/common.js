!
function(e, n) {
    function t() {
        var e = window.innerWidth,
        t = document.documentElement.clientWidth,
        o = e < t ? e: t;
        o >= 750 && (o = 750),
        n.documentElement.style.fontSize = o / 750 * 75 + "px"
    }
    var o = "onorientationchange" in e ? "orientationchange": "resize",
    i = null;
    e.addEventListener(o,
    function() {
        clearTimeout(i),
        i = setTimeout(t, 0)
    },
    !1),
    e.addEventListener("pageshow",
    function(e) {
        e.persisted && (clearTimeout(i), i = setTimeout(t, 0))
    },
    !1),
    t(),
    document.documentElement.setAttribute("data-dpr", "1");
    for (var d = navigator.userAgent,
    m = ["Android", "iPhone", "SymbianOS", "Windows Phone", "iPad", "iPod"], u = !0, a = 0; a < m.length; a++) if (d.indexOf(m[a]) > 0) {
        u = !1;
        break
    }
    document.documentElement.style.margin = "0 auto",
    u ? (document.documentElement.style.maxWidth = "750px", console.log("PC"), console.log("data-dpr=2"),document.documentElement.setAttribute("data-dpr", "2")) : (console.log("no PC"), console.log("data-dpr=1"), document.documentElement.style.maxWidth = "100%",document.documentElement.setAttribute("data-dpr", "1"))
} (window, document);