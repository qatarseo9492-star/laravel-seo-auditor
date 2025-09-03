document.addEventListener('DOMContentLoaded', function () {
    // Tab functionality
    window.openTab = function (event, tabName) {
        let i, tabcontent, tablinks;
        tabcontent = document.getElementsByClassName("tab-content");
        for (i = 0; i < tabcontent.length; i++) {
            tabcontent[i].style.display = "none";
        }
        tablinks = document.getElementsByClassName("tab-button");
        for (i = 0; i < tablinks.length; i++) {
            tablinks[i].className = tablinks[i].className.replace(" active", "");
        }
        document.getElementById(tabName).style.display = "block";
        event.currentTarget.className += " active";
    };

    // Animate the header gauge
    const gaugeElement = document.querySelector('.header-gauge');
    if (gaugeElement) {
        const gaugeProgress = document.getElementById('dailyUsageGauge');
        const value = parseInt(gaugeElement.dataset.value, 10);
        const max = parseInt(gaugeElement.dataset.max, 10);
        const percentage = Math.min(100, (value / max) * 100);

        const circumference = 2 * Math.PI * 15.9155; // Radius from SVG path
        const offset = circumference - (percentage / 100) * circumference;

        gaugeProgress.style.strokeDasharray = `${circumference} ${circumference}`;
        gaugeProgress.style.strokeDashoffset = circumference;
        
        // Trigger animation after a short delay
        setTimeout(() => {
            gaugeProgress.style.strokeDashoffset = offset;
        }, 100);
    }
});
