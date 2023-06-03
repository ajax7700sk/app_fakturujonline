
export default class ScrollTo {

    /**
     * Main mathod
     */
    static init() {
        ScrollTo._doScrollingOnPageLoad();

        //scrolling on same page
        const elements = document.querySelectorAll('.js-scroll-to');

        if (elements.length > 0) {
            for (let i = 0; i < elements.length; ++i) {
                elements[i].addEventListener('click', function (e) {
                    let target = elements[i].getAttribute('href');
                    let hash = ScrollTo._getHashFromTargetHref(target);
                    const apartment = elements[i].dataset.apartment;
                    const select = document.querySelector('.apartment-input');

                    if (apartment && select) {
                        select.value = apartment;
                    }

                    if(ScrollTo._canBeHashScrolledOnPage(target)) {
                        e.preventDefault();
                        ScrollTo._doScrolling(hash, 500);
                    }

                });
            }

        }

    }

    /**
     * Get element Y axis
     *
     * @param {string} query Element identificator
     * @return {number}
     */
    static getElementY(query) {
        let offset = 0;
        let element = document.querySelector(query);

        return window.pageYOffset + document.querySelector(query).getBoundingClientRect().top - offset /* - document.getElementById('menu-container').offsetHeight*/; // minus scrolled header
    }

    /**
     * Scroll to element if exists
     *
     * @param {string} element Element identificator
     * @param {number} duration
     */
    static _doScrolling(element, duration) {
        // Check if element exist in DOM
        if(! ScrollTo._existsElementInDom(document.querySelector(element))) {
            return;
        }

        let startingY = window.pageYOffset;
        let elementY = ScrollTo.getElementY(element);
        // If element is close to page's bottom then window will scroll only to some position above the element.
        let targetY = document.body.scrollHeight - elementY < window.innerHeight ? document.body.scrollHeight - window.innerHeight : elementY;
        let diff = targetY - startingY;
        // Easing function: easeInOutCubic
        // From: https://gist.github.com/gre/1650294
        let easing = function (t) { return t<.5 ? 4*t*t*t : (t-1)*(2*t-2)*(2*t-2)+1 };
        let start;

        if (!diff) return;

        // Bootstrap our animation - it will get called right before next frame shall be rendered.
        window.requestAnimationFrame(function step(timestamp) {
            if (!start) start = timestamp;
            // Elapsed miliseconds since start of scrolling.
            let time = timestamp - start;
            // Get percent of completion in range [0, 1].
            let percent = Math.min(time / duration, 1);
            // Apply the easing.
            // It can cause bad-looking slow frames in browser performance tool, so be careful.
            percent = easing(percent);

            window.scrollTo(0, startingY + diff * percent);

            // Proceed with animation as long as we wanted it to.
            if (time < duration) {
                window.requestAnimationFrame(step);
            } else {
                // Update location href
                //window.location.href = ScrollTo._getHashFromTargetHref(element);
            }
        })
    }

    /**
     * Scroll to element if there is hash in url after page load
     *
     * @private
     */
    static _doScrollingOnPageLoad() {
        let target = window.location.hash;
        let hash = ScrollTo._getHashFromTargetHref(target);

        if(! ScrollTo._canBeHashScrolledOnPage(target)) {
            return;
        }

        // takes care of some browsers issue
        setTimeout(function () {

            try {
                //check if selector is valid
                document.querySelector(target);
            } catch (error) {
                return;
            }

            ScrollTo._doScrolling(target, 500)

        }, 50);
    }

    /**
     * Check if target href target can be scrolled in current page
     *
     * @param {string} target
     * @private
     * @return boolean
     */
    static _canBeHashScrolledOnPage(target) {
        //split target href into path part and hash part
        let parts = target.split('#');
        let path = parts[0];
        let hash = parts[1];
        let currentPath = window.location.pathname;

        if ((currentPath === path || path === '') && hash !== '') {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @param {string} target
     * @return string
     * @private
     */
    static _getHashFromTargetHref(target) {
        let parts = target.split('#');
        let path = parts[0];
        let hash = parts[1];

        return '#' + hash;
    }

    /**
     * Check by selector if element exists in DOM
     *
     * @param {Element|null} element
     * @private
     */
    static _existsElementInDom(element) {

        if (document.body.contains(element)) {
            return true;
        } else {
            return false;
        }
    }



}
