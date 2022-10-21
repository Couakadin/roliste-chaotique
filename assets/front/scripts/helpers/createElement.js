/**
 *
 * @param {string} className
 * @returns {HTMLElement}
 */
export function createDivWithClass(className) {
    let div = document.createElement('div');
    div.setAttribute('class', className);
    return div;
}