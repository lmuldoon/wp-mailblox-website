
(function ($) {


    jQuery(window).on('resize', function () {

        var maxHeight = 0,
            items = jQuery('.tab-panel');
        items.each(function () {
            maxHeight = (jQuery(this).find('.inner').innerHeight() > maxHeight ? jQuery(this).find('.inner').innerHeight() : maxHeight);
        });
        //Assign maximum height to children 
        //items.height(maxHeight);
        //Assign the largest height to the parent only
        //jQuery('.tab-panel-container').height(maxHeight);
    });

    jQuery(window).trigger('resize');

})(jQuery);


(function ($) {

    $('.js-tabs').each((index, tabsContainer) => {
        const tablist = tabsContainer.querySelector('.tab-buttons');
        const tabs = tabsContainer.querySelectorAll('.tab-button');
        const panels = tabsContainer.querySelectorAll('.tab-panel');
        const container = tabsContainer; // container for height adjustment

        tablist.setAttribute('role', 'tablist');

        // Initialize panels
        panels.forEach((panel, i) => {
            panel.setAttribute('role', 'tabpanel');
            panel.setAttribute('tabindex', '-1');
            if (i === 0) {
                panel.style.display = 'block';
            } else {
                panel.style.display = 'none';
            }
        });

        // Initialize tabs
        tabs.forEach((tab, i) => {
            tab.setAttribute('role', 'tab');
            tab.parentNode.setAttribute('role', 'presentation');
            tab.setAttribute('tabindex', i === 0 ? '0' : '-1');
            if (i === 0) tab.setAttribute('aria-selected', 'true');

            $(tab).on('click', function (event) {
                event.preventDefault();
                if (tab.hasAttribute('aria-selected')) return;
                switchTab(tab);
            });

            $(tab).on('keydown', function (event) {
                const keys = { 'ArrowLeft': -1, 'ArrowRight': 1, 'ArrowDown': 'down' };
                const dir = keys[event.key];
                if (!dir) return;

                event.preventDefault();
                if (dir === 'down') { panels[i].focus(); return; }
                const newTab = tabs[i + dir];
                if (newTab) switchTab(newTab);
            });
        });

        function switchTab(newTab) {
            const oldTab = tablist.querySelector('[aria-selected]');
            const oldIndex = Array.prototype.indexOf.call(tabs, oldTab);
            const newIndex = Array.prototype.indexOf.call(tabs, newTab);

            const oldPanel = panels[oldIndex];
            const newPanel = panels[newIndex];

            // Update tab ARIA
            oldTab.removeAttribute('aria-selected');
            oldTab.setAttribute('tabindex', '-1');
            newTab.setAttribute('aria-selected', 'true');
            newTab.removeAttribute('tabindex');
            newTab.focus();

            // Hide old panel
            oldPanel.style.display = 'none';

            // Show new panel
            newPanel.style.display = 'block';

            // Adjust container height
            //container.style.height = newPanel.scrollHeight + 'px';
        }

        // Set initial container height
        //container.style.height = panels[0].scrollHeight + 'px';

        // Update height on window resize
        $(window).on('resize', function () {
            const activeIndex = Array.prototype.findIndex.call(tabs, t => t.hasAttribute('aria-selected'));
            //container.style.height = panels[activeIndex].scrollHeight + 'px';
        });
    });

})(jQuery);