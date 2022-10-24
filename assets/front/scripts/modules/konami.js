const keySequence = [];
let konamiString = '';
const konamiCode = [
    'ArrowUp',
    'ArrowUp',
    'ArrowDown',
    'ArrowDown',
    'ArrowLeft',
    'ArrowRight',
    'ArrowLeft',
    'ArrowRight',
    'b',
    'a'
];

document.addEventListener('keyup', function (e) {
    keySequence.push(e.key);
    keySequence.splice(
        -konamiCode.length - 1,
        keySequence.length - konamiCode.length
    );
    konamiString = konamiCode.join('');

    if (keySequence.join('').includes(konamiString)) {
        document.badgeKonami.submit()
    }
});
