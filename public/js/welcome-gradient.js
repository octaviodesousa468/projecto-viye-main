(function () {
    var body = document.body;
    if (!body) return;

    var reduceMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    if (reduceMotion) return;

    var colorA = [0, 33, 104];   // #002168
    var colorB = [54, 118, 255]; // #3676ff

    function mix(c1, c2, t) {
        return [
            Math.round(c1[0] + (c2[0] - c1[0]) * t),
            Math.round(c1[1] + (c2[1] - c1[1]) * t),
            Math.round(c1[2] + (c2[2] - c1[2]) * t)
        ];
    }

    function rgb(c) {
        return 'rgb(' + c[0] + ', ' + c[1] + ', ' + c[2] + ')';
    }

    function tick(time) {
        var phase = ((time || 0) / 8000) * Math.PI * 2;
        var t = (Math.sin(phase) + 1) / 2;
        var angle = 45 + 12 * Math.sin(phase * 0.7);

        var first = mix(colorA, colorB, t);
        var second = mix(colorB, colorA, t);

        body.style.background = 'linear-gradient(' + angle.toFixed(2) + 'deg, ' + rgb(first) + ', ' + rgb(second) + ')';
        window.requestAnimationFrame(tick);
    }

    window.requestAnimationFrame(tick);
})();
