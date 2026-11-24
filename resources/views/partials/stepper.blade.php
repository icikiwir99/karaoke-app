<style>
    .qty-stepper > input::-webkit-outer-spin-button,
    .qty-stepper > input::-webkit-inner-spin-button { -webkit-appearance: none; margin: 0; }
    .qty-stepper { display: inline-flex; align-items: center; gap: 2px; background: #020617; border: 1px solid #334155; border-radius: .65rem; padding: 2px; }
    .qty-stepper > input { width: 2.5rem !important; border: 0 !important; background: transparent !important; padding: 0 !important; text-align: center; -moz-appearance: textfield; appearance: textfield; outline: none; }
    .qty-stepper > button { width: 1.9rem; height: 1.9rem; border-radius: .5rem; display: flex; align-items: center; justify-content: center; color: #e2e8f0; font-weight: 800; font-size: 1.05rem; background: #1e293b; transition: transform .15s, background .2s, opacity .2s; }
    .qty-stepper > button:hover:not(:disabled) { background: #7c3aed; }
    .qty-stepper > button:active:not(:disabled) { transform: scale(.85); }
    .qty-stepper > button:disabled { opacity: .3; cursor: not-allowed; }
</style>
<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('input[type="number"][data-stepper]').forEach(function (input) {
        if (input.closest('.qty-stepper')) return;
        const wrap = document.createElement('div'); wrap.className = 'qty-stepper';
        const mk = t => { const b = document.createElement('button'); b.type = 'button'; b.textContent = t; return b; };
        const minus = mk('-'), plus = mk('+');
        input.parentNode.insertBefore(wrap, input);
        wrap.append(minus, input, plus);

        const num = (v, d) => { const n = parseFloat(v); return isNaN(n) ? d : n; };
        const min = () => num(input.min, 0);
        const max = () => input.max === '' ? Infinity : num(input.max, Infinity);
        const step = () => num(input.step, 1) || 1;
        const clamp = v => Math.min(Math.max(v, min()), max());
        function refresh() { const v = clamp(num(input.value, min())); minus.disabled = v <= min(); plus.disabled = v >= max(); }
        function set(v) {
            input.value = clamp(v);
            input.dispatchEvent(new Event('input',  { bubbles: true }));
            input.dispatchEvent(new Event('change', { bubbles: true }));
            refresh();
        }
        minus.addEventListener('click', () => set(num(input.value, min()) - step()));
        plus.addEventListener('click',  () => set(num(input.value, min()) + step()));
        input.addEventListener('input', refresh);
        refresh();
    });
});
</script>