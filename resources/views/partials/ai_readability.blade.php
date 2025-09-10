<div class="unified-card unified-card--blue" id="aiContentCheckerCard">
    <div style="display:flex; justify-content:center; align-items:center; gap:10px; margin-bottom:16px;">
        <span class="c-icon pulse">🤖</span>
        <h3 class="t-grad" style="font-weight:900;margin:0; font-size: 22px;">AI Readability & Humanizer</h3>
    </div>
    <div id="humanizerCard">
        <div id="humanizerWheelContainer" style="display:grid; place-items:center;">
            <div class="mw mw-sm" id="mwHumanizer">
                <div class="mw-ring"></div>
                <div class="mw-center">0%</div>
            </div>
        </div>
         <div id="humanizerResult">
            <div style="display:flex; flex-direction: column; align-items:center; justify-content:center; height:100%; color: var(--sub); opacity: 0.7; text-align: center;">
                <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" style="width: 48px; height: 48px; margin-bottom: 8px;"><path d="M12 2C6.48 2 2 6.48 2 12C2 17.52 6.48 22 12 22C17.52 22 22 17.52 22 12C22 6.48 17.52 2 12 2ZM12 20C7.59 20 4 16.41 4 12C4 7.59 7.59 4 12 4C16.41 4 20 7.59 20 12C20 16.41 16.41 20 12 20ZM12 6C11.17 6 10.5 6.67 10.5 7.5C10.5 8.33 11.17 9 12 9C12.83 9 13.5 8.33 13.5 7.5C13.5 6.67 12.83 6 12 6ZM7 12C7 14.76 9.24 17 12 17C14.76 17 17 14.76 17 12H7Z" fill="currentColor"/></svg>
                Run an analysis or check a text snippet to see the human-like score.
            </div>
        </div>
    </div>
    <div style="border-top: 1px solid var(--outline); padding-top: 16px; margin-top: 16px;">
        <textarea id="aiCheckTextarea" placeholder="Or, paste a text snippet here to analyze it separately... (Min: 50 characters)" style="width: 100%; height: 100px; background: #081220; border: 1px solid #1c3d52; border-radius: 10px; padding: 12px; color: var(--ink); font-size: 13px; resize: vertical;"></textarea>
        <button id="aiCheckBtn" class="btn btn-green" style="width: 100%; margin-top: 10px;"><span class="btn-icon">🤖</span><span>Check Snippet</span></button>
    </div>
</div>
