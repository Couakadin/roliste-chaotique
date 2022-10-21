export class TouchDragCarousel {
    /**
     *
     * @param {Carousel} carousel
     */
    constructor(carousel) {
        carousel.container.addEventListener('touchstart', this.startDrag.bind(this), true);
        carousel.container.addEventListener('touchmove', this.drag.bind(this), true);
        carousel.container.addEventListener('touchend', this.endDrag.bind(this), true);
        carousel.container.addEventListener('touchcancel', this.endDrag.bind(this), true);

        carousel.container.addEventListener('dragstart', e => e.preventDefault());
        carousel.container.addEventListener('mousedown', this.startDrag.bind(this), true);
        carousel.container.addEventListener('mousemove', this.drag.bind(this), true);
        carousel.container.addEventListener('mouseup', this.endDrag.bind(this), true);

        this.carousel = carousel;
    }

    /**
     * Starts drag
     *
     * @param {MouseEvent|TouchEvent} e
     */
    startDrag(e) {
        if (e.touches && e.touches.length > 1) {
            return;
        } else if (e.touches) {
            e = e.touches[0];
        }

        this.origin = {x: e.screenX, y: e.screenY};
        this.width = this.carousel.containerWidth;
        this.carousel.disableTransition();
    }

    /**
     *
     * @param {MouseEvent|TouchEvent} e
     */
    drag(e) {
        e.preventDefault();

        if (this.origin) {
            const point = e.touches ? e.touches[0] : e;
            const translate = {x: point.screenX - this.origin.x, y: point.screenY - this.origin.y};
            if (e.touches && Math.abs(translate.x) > Math.abs(translate.y)) {
                e.preventDefault();
                e.stopPropagation();
            }
            const baseTranslate = this.carousel.currentSlide * -100 / this.carousel.items.length;
            this.lastTranslate = translate;
            this.carousel.translate(baseTranslate + 100 * translate.x / this.width);
        }
    }

    /**
     *
     * Ends of drag
     *
     * @param {MouseEvent|TouchEvent} e
     */
    endDrag(e) {
        console.log(this.origin)
        if (this.origin && this.lastTranslate) {
            e.preventDefault();
            this.carousel.enableTransition();
            if (Math.abs(this.lastTranslate.x / this.carousel.carouselWidth) > 0.1) {
                if (this.lastTranslate.x < 0) {
                    this.carousel.next();
                } else {
                    this.carousel.prev();
                }
            } else {
                this.carousel.goToSlide(this.carousel.currentSlide);
            }
        }
        this.origin = null;
        this.lastTranslate = null;
    }
}