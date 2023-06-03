/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

import objectFitImages from 'object-fit-images';
import AOS from 'aos';

import Menu from './components/Menu';
import ScrollTo from "./components/ScrollTo";
import Form from "./components/Form";
import MicroModal from "micromodal";
import Flashmsgs from "./components/Flashmsgs";

document.addEventListener('DOMContentLoaded', (event) => {
    Menu.handleTopBar();
    Menu.setScrolling();
    Menu.listenOpen();
    Menu.handleUserMenu();
    Form.init();
    ScrollTo.init();

    AOS.init({
        once: true,
        duration: 1000,
    });

    MicroModal.init({
        disableScroll: false,
    });

    Flashmsgs.init();

    /**
     * Handle triggers which open forgotten pass modal
     * @type {NodeListOf<Element>}
     */
    const modalTriggers = document.querySelectorAll('.trigger-forgotten-pass-modal');
    if (modalTriggers.length > 0) {
        const menu = document.getElementById('menu-container');
        modalTriggers.forEach( trigger => {
            trigger.addEventListener('click', () => {
                const userMenu = document.getElementById('user-menu-toggle');
                if (userMenu) {
                    userMenu.checked = false;
                }
                if (menu) {
                    menu.classList.remove('menu-container--user-menu-open');
                }
                MicroModal.show('forgotten-pass-modal');
            });
        });
    }

    objectFitImages();
});
