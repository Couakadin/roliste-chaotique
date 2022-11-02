import {createDivWithClass} from '../helpers/createElement';
import {TouchDragCarousel} from '../helpers/touchDrag';

document.addEventListener('DOMContentLoaded', () => {
    const carouselHome = document.querySelector('[data-carousel="home"]');

    if (carouselHome) {
        new Carousel(carouselHome, {
            slidesToScroll: 2,
            slidesVisible : 4,
            infinite      : true
        });
    }
});

class Carousel {
    /**
     *
     * @param {HTMLElement} element
     * @param {Object} options
     * @param {Object} options options.slidesToScroll "Number of elements to scroll"
     * @param {Object} options options.slidesVisible "Number of elements visible per slide"
     * @param {Object} options options.infinite "Loop infinite in carousel"
     */
    constructor(element, options = {}) {
        this.element = element;
        this.options = Object.assign({}, {
            slidesToScroll: 1,
            slidesVisible : 1,
            infinite      : false
        }, options);
        this.isMobile = false;
        this.isTablet = false;
        this.currentSlide = 0;

        this.children = [].slice.call(this.element.children);
        this.container = createDivWithClass('carousel-container');
        this.element.appendChild(this.container);
        this.element.setAttribute('tabindex', '0');

        this.items = this.children.map((child) => {
            const item = createDivWithClass('carousel-item');
            item.appendChild(child);
            return item;
        })
        if (this.options.infinite) {
            this.offset = this.options.slidesVisible + this.options.slidesToScroll;
            if (this.offset > this.children.length) {
                console.error('Not sufficient element in carousel', element);
            }
            this.items = [
                ...this.items.slice(this.items.length - this.offset).map(item => item.cloneNode(true)),
                ...this.items,
                ...this.items.slice(0, this.offset).map(item => item.cloneNode(true))
            ]
            this.goToSlide(this.offset, false);
        }
        this.items.forEach(item => this.container.appendChild(item));

        this.setStyle();
        this.createPagination();
        this.onWindowResize();

        // Events
        window.addEventListener('resize', this.onWindowResize.bind(this));
        this.element.addEventListener('keyup', (e) => {
            if ('ArrowRight' === e.key || 'Right' === e.key) {
                this.next();
            } else if ('ArrowLeft' === e.key || 'Left' === e.key) {
                this.prev();
            }
        })
        if (this.options.infinite) {
            this.container.addEventListener('transitionend', this.resetInfinite.bind(this));
        }

        new TouchDragCarousel(this);
    };

    /**
     * Set the right dimensions to the carousel elements
     */
    setStyle() {
        this.ratio = this.items.length / this.slidesVisible;
        this.container.style.width = (this.ratio * 100) + '%';
        this.items.forEach(item => item.style.width = ((100 / this.slidesVisible) / this.ratio) + '%');
    }

    createPagination() {
        const nextButton = createDivWithClass('carousel-next');
        const prevButton = createDivWithClass('carousel-prev');
        this.element.appendChild(nextButton);
        this.element.appendChild(prevButton);
        nextButton.addEventListener('click', this.next.bind(this));
        prevButton.addEventListener('click', this.prev.bind(this));
    }

    next() {this.goToSlide(this.currentSlide + this.slidesToScroll);}

    prev() {this.goToSlide(this.currentSlide - this.slidesToScroll);}

    disableTransition() {this.container.style.transition = 'none';}

    enableTransition() {this.container.style.transition = '';}

    translate(percent) {this.container.style.transform = `translate3d(${percent}%, 0, 0)`;}

    /**
     * Move the slide to the target
     *
     * @param {number} index
     * @param {boolean} animation
     */
    goToSlide(index, animation = true) {
        if (index < 0) {
            index = this.items.length - this.slidesVisible;
        } else if (index >= this.items.length || (undefined === this.items[this.currentSlide + this.slidesVisible]) && index > this.currentSlide) {
            index = 0;
        }

        const translateX = index * -100 / this.items.length;

        if (false === animation) {this.disableTransition();}

        this.translate(translateX);

        this.container.offsetHeight;
        if (false === animation) {this.enableTransition();}

        this.currentSlide = index;
    }

    /**
     * Move the container the get the impression of an infinite loop
     */
    resetInfinite() {
        if (this.currentSlide <= this.options.slidesToScroll) {
            this.goToSlide(this.currentSlide + this.items.length - 2 * this.offset, false);
        } else if (this.currentSlide >= this.items.length - this.offset) {
            this.goToSlide(this.currentSlide - (this.items.length - 2 * this.offset), false);
        }
    }

    onWindowResize() {
        const mobile = window.innerWidth < 768;
        const tablet = window.innerWidth < 1200;
        if (mobile !== this.isMobile) {
            this.isMobile = mobile;
            this.setStyle();
        } else if (tablet !== this.isTablet) {
            this.isTablet = tablet;
            this.setStyle();
        }
    }

    /**
     *
     * @returns {number}
     */
    get slidesToScroll() {
        let value = this.options.slidesToScroll;
        if (this.isMobile) {
            value = 1;
        } else if (this.isTablet) {
            value = 2;
        }
        return value;
    }

    /**
     *
     * @returns {number}
     */
    get slidesVisible() {
        let value = this.options.slidesVisible;
        if (this.isMobile) {
            value = 1;
        } else if (this.isTablet) {
            value = 2;
        }
        return value;
    }

    /**
     *
     * @returns {number}
     */
    get containerWidth() {return this.container.offsetWidth;}

    /**
     *
     * @returns {number}
     */
    get carouselWidth() {return this.element.offsetWidth;}
}
