
export default class Flashmsgs {

    static init() {
        const flashmsgs = document.querySelectorAll('.flash-messages');

        if (flashmsgs.length > 0) {
            flashmsgs.forEach(msg => {
                const closer = msg.querySelector('button');
                if (closer) {
                    closer.addEventListener('click', (e) => {
                        e.preventDefault();
                        msg.classList.add('flash-messages--fade-out');
                        setTimeout(() => {
                            msg.style.display = 'none';
                        },250);
                    })
                }
                setTimeout(() => {
                    msg.classList.add('flash-messages--fade-out');
                },3000);
                setTimeout(() => {
                    msg.style.display = 'none';
                },3250);
            });
        }
    }
}
