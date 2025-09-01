/* semantic-shims.v4.js
 * Ensures a safe global `co` namespace exists before any analyzer scripts run.
 */
;(function (root) {
  var g = (typeof globalThis !== 'undefined') ? globalThis
        : (typeof window !== 'undefined') ? window
        : (typeof self !== 'undefined') ? self
        : this;

  // Create the namespace once
  if (!g.co) {
    g.co = {
      version: 'shim-4',
      state: {},
      data: {},
      ui: {}
    };
  }

  // Defensive no-op helpers
  var noop = function(){};
  var maybeNoop = [
    'init', 'render', 'renderBars',
    'updateWheel', 'setScores', 'setSuggestions',
    'hydrate', 'mount', 'fetchAndAnalyze', 'applyTheme'
  ];
  maybeNoop.forEach(function (k) {
    if (typeof g.co[k] !== 'function') g.co[k] = noop;
  });

  // Simple event bus
  if (!g.co.on) {
    var bus = {};
    g.co.on = function (evt, fn) {
      (bus[evt] = bus[evt] || []).push(fn);
      return function(){ bus[evt] = (bus[evt]||[]).filter(f=>f!==fn); };
    };
    g.co.emit = function (evt, payload) {
      (bus[evt] || []).slice().forEach(function (fn) {
        try { fn(payload); } catch (e) { console.error('[co.emit]', evt, e); }
      });
    };
  }
})(this);
