(function () {
    'use strict';

    function makeButton(label, ariaLabel, symbol) {
        const button = document.createElement('button');
        button.type = 'button';
        button.className = 'table-scroll-button';
        button.setAttribute('aria-label', ariaLabel);
        button.setAttribute('title', ariaLabel);
        button.textContent = symbol;
        return button;
    }

    function initialiseTableScroll(tableWrap) {
        if (tableWrap.dataset.scrollControlsReady === '1') return;
        tableWrap.dataset.scrollControlsReady = '1';

        const parent = tableWrap.parentNode;
        if (!parent) return;

        const shell = document.createElement('div');
        shell.className = 'table-scroll-shell';
        parent.insertBefore(shell, tableWrap);
        shell.appendChild(tableWrap);

        const controls = document.createElement('div');
        controls.className = 'table-scroll-controls';
        controls.hidden = true;

        const label = document.createElement('span');
        label.className = 'table-scroll-label';
        label.textContent = 'Geser tabel ke kiri atau kanan';

        const leftButton = makeButton('‹', 'Geser tabel ke kiri', '‹');
        const rightButton = makeButton('›', 'Geser tabel ke kanan', '›');

        controls.appendChild(label);
        controls.appendChild(leftButton);
        controls.appendChild(rightButton);
        shell.insertBefore(controls, tableWrap);

        const update = function () {
            const maxScroll = Math.max(0, tableWrap.scrollWidth - tableWrap.clientWidth);
            const overflowing = maxScroll > 4;

            controls.hidden = !overflowing;
            leftButton.disabled = !overflowing || tableWrap.scrollLeft <= 2;
            rightButton.disabled = !overflowing || tableWrap.scrollLeft >= maxScroll - 2;
        };

        const scrollAmount = function () {
            return Math.max(220, Math.round(tableWrap.clientWidth * 0.72));
        };

        leftButton.addEventListener('click', function () {
            tableWrap.scrollBy({ left: -scrollAmount(), behavior: 'smooth' });
        });

        rightButton.addEventListener('click', function () {
            tableWrap.scrollBy({ left: scrollAmount(), behavior: 'smooth' });
        });

        tableWrap.addEventListener('scroll', update, { passive: true });
        window.addEventListener('resize', update, { passive: true });

        if ('ResizeObserver' in window) {
            const observer = new ResizeObserver(update);
            observer.observe(tableWrap);
            const table = tableWrap.querySelector('table');
            if (table) observer.observe(table);
        }

        // Re-evaluate after fonts and layout settle.
        window.requestAnimationFrame(update);
        window.setTimeout(update, 250);
    }

    function initialiseAllTables() {
        document.querySelectorAll('.table-wrap').forEach(initialiseTableScroll);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initialiseAllTables);
    } else {
        initialiseAllTables();
    }
})();
