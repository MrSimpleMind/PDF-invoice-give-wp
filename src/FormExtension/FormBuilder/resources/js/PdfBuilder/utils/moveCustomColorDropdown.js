export const moveCustomColorDropdown = () => {
    const popoverSlot = document.querySelector('#root > div > div.popover-slot');

    if (!!popoverSlot) {
        // Create an observer instance
        const observer = new MutationObserver(function (mutations) {
            const customColorDropdown = popoverSlot.querySelector(
                '.components-color-palette__custom-color-dropdown-content'
            );
            const modal = document.querySelector('.components-modal__screen-overlay');
            const colorButton = modal.querySelector('.components-color-palette__custom-color-button');

            if (!!customColorDropdown && !!modal && !!colorButton) {
                modal.appendChild(popoverSlot);

                modal.addEventListener(
                    'click',
                    function (event) {
                        if (event.target === colorButton) {
                            document.querySelector('.interface-interface-skeleton').after(popoverSlot);
                            return;
                        }

                        if (!!customColorDropdown && event.target.closest('.components-modal__frame')) {
                            customColorDropdown.remove();
                            document.querySelector('.interface-interface-skeleton').after(popoverSlot);
                        }
                    },
                    false
                );
            }
        });

        // Configuration of the observer
        const config = {
            attributes: true,
            childList: true,
            characterData: true,
        };

        // Pass in the target node, as well as the observer options
        observer.observe(popoverSlot, config);
    }
};
