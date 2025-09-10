document.addEventListener('DOMContentLoaded', () => {
    const aiCheckTextarea = document.getElementById('aiCheckTextarea');
    const aiCheckBtn = document.getElementById('aiCheckBtn');
    const humanizerResult = document.getElementById('humanizerResult');
    const humanizerWheel = document.getElementById('mwHumanizer');

    // Expose the render function to the global scope so the main script can call it
    window.renderHumanizerComponent = (data) => {
        renderHumanizerResult(data);
    };

    const setWheel = (el, score, prefix) => {
        if (!el) return;
        const ring = el.querySelector('.mw-ring');
        const num = el.querySelector('.mw-center');
        const bandName = s => s >= 80 ? 'good' : (s >= 60 ? 'warn' : 'bad');
        const b = bandName(score);
        el.classList.remove('good', 'warn', 'bad');
        el.classList.add(b);
        ring.style.setProperty('--v', score);
        num.textContent = (prefix ? prefix + ' ' : '') + score + '%';
    };

    const renderHumanizerResult = (data) => {
        const { human_score, suggestions, recommendation, badge_type, google_search_url, scoring_method } = data;
        
        if (humanizerWheel) {
            setWheel(humanizerWheel, human_score, '');
        }

        const badgeSvgs = {
            success: `<svg class="badge-icon success" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg"><path class="glow" d="M32 58C46.3594 58 58 46.3594 58 32C58 17.6406 46.3594 6 32 6C17.6406 6 6 17.6406 6 32C6 46.3594 17.6406 58 32 58Z" stroke="url(#paint0_linear_success)" stroke-width="4"/><path d="M22 32.5L28.5 39L43 24" stroke="var(--green-1)" stroke-width="5" stroke-linecap="round" stroke-linejoin="round"/><defs><linearGradient id="paint0_linear_success" x1="32" y1="6" x2="32" y2="58" gradientUnits="userSpaceOnUse"><stop stop-color="var(--green-2)"/><stop offset="1" stop-color="var(--green-1)"/></linearGradient></defs></svg>`,
            warning: `<svg class="badge-icon warning" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg"><path class="glow" d="M32 58C46.3594 58 58 46.3594 58 32C58 17.6406 46.3594 6 32 6C17.6406 6 6 17.6406 6 32C6 46.3594 17.6406 58 32 58Z" stroke="url(#paint0_linear_warning)" stroke-width="4"/><path d="M32 23V36" stroke="var(--yellow-1)" stroke-width="4" stroke-linecap="round"/><path d="M32 43V44" stroke="var(--yellow-1)" stroke-width="5" stroke-linecap="round"/><defs><linearGradient id="paint0_linear_warning" x1="32" y1="6" x2="32" y2="58" gradientUnits="userSpaceOnUse"><stop stop-color="var(--orange-1)"/><stop offset="1" stop-color="var(--yellow-1)"/></linearGradient></defs></svg>`,
            danger: `<svg class="badge-icon danger" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg"><path class="glow" d="M32 58C46.3594 58 58 46.3594 58 32C58 17.6406 46.3594 6 32 6C17.6406 6 6 17.6406 6 32C6 46.3594 17.6406 58 32 58Z" stroke="url(#paint0_linear_danger)" stroke-width="4"/><path d="M40 24L24 40" stroke="var(--red-1)" stroke-width="4" stroke-linecap="round"/><path d="M24 24L40 40" stroke="var(--red-1)" stroke-width="4" stroke-linecap="round"/><defs><linearGradient id="paint0_linear_danger" x1="32" y1="6" x2="32" y2="58" gradientUnits="userSpaceOnUse"><stop stop-color="var(--pink-1)"/><stop offset="1" stop-color="var(--red-1)"/></linearGradient></defs></svg>`
        };
        
        let suggestionsHtml = '';
        if (badge_type !== 'success' && suggestions) {
            suggestionsHtml = `<div id="humanizerSuggestionsWrapper"><h4 style="color: var(--blue-1); margin: 0 0 8px;">💡 AI Suggestions to Improve:</h4><div style="white-space: pre-wrap; font-size: 13px; line-height: 1.6;">${suggestions}</div></div>`;
        }

        let googleLinkHtml = '';
        if (badge_type !== 'success' && google_search_url) {
            googleLinkHtml = `<a href="${google_search_url}" target="_blank" class="btn btn-blue" style="margin-top: 15px;"><span class="btn-icon">💡</span><span>Find More on Google</span></a>`;
        }
        
        let scoringMethodHtml = `<p style="font-size: 11px; color: var(--sub); opacity: 0.6; margin-top: 10px;">Scoring method: ${scoring_method}</p>`;

        humanizerResult.innerHTML = `
            <div class="badge-icon-wrapper">${badgeSvgs[badge_type] || ''}</div>
            <p class="recommendation">${recommendation}</p>
            ${suggestionsHtml}
            ${googleLinkHtml}
            ${scoringMethodHtml}
        `;
    };

    aiCheckBtn?.addEventListener('click', async () => {
        const text = aiCheckTextarea.value.trim();
        if (text.length < 50) {
            humanizerResult.innerHTML = '<p style="color: var(--orange-1);">Please enter at least 50 characters to analyze.</p>';
            return;
        }

        aiCheckBtn.disabled = true;
        aiCheckBtn.innerHTML = '<span class="c-icon spin">🤖</span> Analyzing...';
        humanizerResult.innerHTML = '<p>Checking AI score and generating suggestions...</p>';

        try {
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            const response = await fetch('/api/ai-readability-check', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({ text })
            });

            if (!response.ok) {
                const errorData = await response.json();
                throw new Error(errorData.error || `HTTP error! status: ${response.status}`);
            }

            const result = await response.json();
            renderHumanizerResult(result);

        } catch (error) {
            let message = error.message || 'An unknown error occurred.';
            humanizerResult.innerHTML = `<p style="color: var(--red-1);">Error: ${message}</p>`;
        } finally {
            aiCheckBtn.disabled = false;
            aiCheckBtn.innerHTML = '<span class="btn-icon">🤖</span><span>Check Snippet</span>';
        }
    });
});
