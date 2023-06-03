import { disableBodyScroll, enableBodyScroll, clearAllBodyScrollLocks } from 'body-scroll-lock';

/**
 * Handle menu
 */
export default class Menu {
    
    /**
     * Hide and show menu on scrolling.
     */
    static setScrolling() {
        let prevScrollPos = window.pageYOffset;
        
        function init() {
            const menu = document.getElementById('menu-container');
            const header = document.getElementById('header');
            const currentScrollPos = window.pageYOffset;

            // if menu's height is bigger than scrolled area start scrolling
            if (menu && header && header.offsetTop < currentScrollPos) {
                menu.classList.add('menu-container--is-scrolled');
                
                if (currentScrollPos > prevScrollPos) {
                    menu.classList.remove('menu-container--is-scrolled-top');
                } else {
                    menu.classList.add('menu-container--is-scrolled-top');
                }
            } else if (menu) {
                menu.classList.remove('menu-container--is-scrolled');
                menu.classList.remove('menu-container--is-scrolled-top');
            }
            
            prevScrollPos = currentScrollPos;
        }
        
        // init menu on load
        init();
        window.onscroll = function () {
            // call menu on scroll
            init();
        };
    }

    /**
     * Set overflow hidden to body if menu is open (ban background-scrolling).
     */
    static listenOpen() {
        const checkbox = document.getElementById('nav-toggle'),
            menu = document.getElementById('menu-container'),
            navList = document.getElementById('nav-list');

        if (checkbox) {
            checkbox.addEventListener('change', function(){
                if (checkbox.checked) {
                    disableBodyScroll(navList);
                    menu.classList.add('menu-container--is-open');
                } else {
                    enableBodyScroll(navList);
                    menu.classList.remove('menu-container--is-open');
                }
            });
        }
    }

    static handleUserMenu() {
        const input = document.getElementById('user-menu-toggle'),
            userMenu = document.getElementById('user-menu'),
            menu = document.getElementById('menu-container');

        if (input && menu && userMenu) {
            input.addEventListener('change', () => {
                if (input.checked) {
                    menu.classList.add('menu-container--user-menu-open');
                    //disableBodyScroll(userMenu);
                } else {
                    menu.classList.remove('menu-container--user-menu-open');
                    //enableBodyScroll(userMenu);
                }
            });
        }
    }

    static handleTopBar() {
        const topBar = document.getElementById('top-bar');
        const closer = document.getElementById('top-bar-closer');

        if (topBar && closer) {
            closer.addEventListener('click', (e) => {
                e.preventDefault();
                topBar.classList.add('top-bar--closed');
            })
        }
    }
}
