import './bootstrap';

import { getNavLogoSrc, scheduleNavLogoWarm, warmNavLogoCache } from './nav-logo-cache';
import Alpine from 'alpinejs';
import collapse from '@alpinejs/collapse';
import intersect from '@alpinejs/intersect';

Alpine.plugin(collapse);
Alpine.plugin(intersect);

/** Home hero “Featured Company” — matches HomePage.tsx AnimatePresence mode="wait" (leave 500ms, then swap, then enter; timeout 520ms). */
document.addEventListener('alpine:init', () => {
  Alpine.data('siteNavbar', (config = {}) => ({
    heroTopIsDark: config.heroTopIsDark ?? false,
    companyLogoUrls: Array.isArray(config.companyLogoUrls) ? config.companyLogoUrls : [],
    logosWarmed: false,
    isScrolled: false,
    mobileOpen: false,
    mobileCompaniesOpen: false,
    get navSolid() {
      return this.isScrolled;
    },
    get navOnDarkHero() {
      return !this.isScrolled && this.heroTopIsDark;
    },
    _onResize: null,
    _onScroll: null,
    _scrollRaf: null,
    logoSrc(url) {
      return getNavLogoSrc(url) || url;
    },
    async init() {
      if (this.companyLogoUrls.length > 0) {
        scheduleNavLogoWarm(this.companyLogoUrls);
        await warmNavLogoCache(this.companyLogoUrls);
        this.logosWarmed = true;
      }

      this._onScroll = () => {
        if (this._scrollRaf != null) return;
        this._scrollRaf = requestAnimationFrame(() => {
          this._scrollRaf = null;
          const next = window.scrollY > 20;
          if (this.isScrolled !== next) this.isScrolled = next;
        });
      };
      this._onScroll();
      window.addEventListener('scroll', this._onScroll, { passive: true });
      this.$watch('mobileOpen', (open) => {
        if (!open) this.mobileCompaniesOpen = false;
        document.documentElement.classList.toggle('site-mobile-menu-open', !!open);
      });
      this._onResize = () => {
        if (window.innerWidth >= 1024 && this.mobileOpen) this.mobileOpen = false;
      };
      window.addEventListener('resize', this._onResize, { passive: true });
    },
    destroy() {
      document.documentElement.classList.remove('site-mobile-menu-open');
      if (this._scrollRaf != null) {
        cancelAnimationFrame(this._scrollRaf);
        this._scrollRaf = null;
      }
      if (this._onScroll) window.removeEventListener('scroll', this._onScroll);
      if (this._onResize) window.removeEventListener('resize', this._onResize);
    },
  }));

  /** Careers — matches Careers.tsx useInView(once, margin -100px); skip animation if reduced motion. */
  Alpine.data('careersPage', (cfg = {}) => ({
    careersInView: false,
    activeJobIndex: null,
    showApplyModal: false,
    applyJobTitle: '',
    applyJobTitleLocked: true,
    init() {
      if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
        this.careersInView = true;
      }
      if (cfg.reopenJobModal) {
        this.applyJobTitle = cfg.jobModalTitle || '';
        this.applyJobTitleLocked = cfg.jobModalLocked !== false;
        this.showApplyModal = true;
        document.documentElement.classList.add('overflow-y-hidden');
      }
    },
    toggleJob(index) {
      if (typeof index !== 'number') return;
      this.activeJobIndex = this.activeJobIndex === index ? null : index;
    },
    openApplyModal(title, lockTitle = true) {
      this.applyJobTitle = title || '';
      this.applyJobTitleLocked = lockTitle !== false;
      this.showApplyModal = true;
      document.documentElement.classList.add('overflow-y-hidden');
    },
    closeApplyModal() {
      this.showApplyModal = false;
      this.applyJobTitle = '';
      this.applyJobTitleLocked = true;
      document.documentElement.classList.remove('overflow-y-hidden');
    },
  }));

  /** Contact — Contact.tsx: useInView ×2 (header block + map); skip motion if reduced. */
  Alpine.data('contactPage', () => ({
    contactInView: false,
    mapInView: false,
    init() {
      if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
        this.contactInView = true;
        this.mapInView = true;
      }
    },
  }));

  /** BlogsPage.tsx — filter, search, pagination, scroll-to on page change. */
  Alpine.data('blogsPage', (posts, categories) => ({
    posts,
    categories,
    selectedCategory: 'All',
    searchQuery: '',
    currentPage: 1,
    postsPerPage: 10,
    filterInView: false,
    featuredInView: false,
    gridInView: false,
    pagInView: false,
    galleryInView: false,
    init() {
      if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
        this.filterInView = true;
        this.featuredInView = true;
        this.gridInView = true;
        this.pagInView = true;
        this.galleryInView = true;
      }
    },
    get filteredPosts() {
      const q = (this.searchQuery || '').toLowerCase();
      return this.posts.filter((post) => {
        const cat = this.selectedCategory === 'All' || post.category === this.selectedCategory;
        const search =
          !q || post.title.toLowerCase().includes(q) || post.excerpt.toLowerCase().includes(q);
        return cat && search;
      });
    },
    get totalPages() {
      const n = this.filteredPosts.length;
      return n === 0 ? 0 : Math.ceil(n / this.postsPerPage);
    },
    get currentPosts() {
      const n = this.filteredPosts.length;
      if (n === 0) return [];
      const tp = Math.ceil(n / this.postsPerPage);
      const cp = Math.min(Math.max(1, this.currentPage), tp);
      const start = (cp - 1) * this.postsPerPage;
      return this.filteredPosts.slice(start, start + this.postsPerPage);
    },
    get featuredPost() {
      return this.currentPosts[0] || null;
    },
    get regularPosts() {
      return this.currentPosts.slice(1);
    },
    selectCategory(cat) {
      this.selectedCategory = cat;
      this.currentPage = 1;
    },
    setSearch(e) {
      this.searchQuery = e.target.value;
      this.currentPage = 1;
    },
    goToPage(page) {
      const tp = this.totalPages;
      if (typeof page !== 'number' || page < 1 || page > tp) return;
      this.currentPage = page;
      window.scrollTo({ top: 400, behavior: 'smooth' });
    },
    pageNumbers() {
      const totalPages = this.totalPages;
      const currentPage = this.currentPage;
      const maxVisiblePages = 5;
      const pages = [];
      if (totalPages <= maxVisiblePages) {
        for (let i = 1; i <= totalPages; i++) pages.push(i);
      } else {
        pages.push(1);
        if (currentPage > 3) pages.push('...');
        const start = Math.max(2, currentPage - 1);
        const end = Math.min(totalPages - 1, currentPage + 1);
        for (let i = start; i <= end; i++) pages.push(i);
        if (currentPage < totalPages - 2) pages.push('...');
        pages.push(totalPages);
      }
      return pages;
    },
  }));

  Alpine.data('aboutPartnershipSlider', (slides = []) => ({
    slides: Array.isArray(slides) ? slides.filter((url) => url && String(url).trim()) : [],
    activeIndex: 0,
    _interval: null,
    get slideTransform() {
      if (this.slides.length <= 1) {
        return 'translateX(0)';
      }

      return `translateX(-${this.activeIndex * 100}%)`;
    },
    init() {
      if (this.slides.length < 2) {
        return;
      }
      if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
        return;
      }
      this._interval = window.setInterval(() => this.next(), 5000);
    },
    destroy() {
      if (this._interval) {
        window.clearInterval(this._interval);
      }
    },
    goTo(index) {
      if (index < 0 || index >= this.slides.length) {
        return;
      }
      this.activeIndex = index;
    },
    next() {
      if (this.slides.length < 2) {
        return;
      }
      this.activeIndex = (this.activeIndex + 1) % this.slides.length;
    },
  }));

  Alpine.data('heroSpotlight', (items, fallbackHeroImage = null) => ({
    items: Array.isArray(items) ? items : [],
    fallbackHeroImage: fallbackHeroImage || null,
    idx: 0,
    visible: true,
    _interval: null,
    _cyclePending: null,
    _cycling: false,
    get heroSlides() {
      return this.items
        .map((item) => {
          const url = item?.heroImage;
          if (url && String(url).trim()) {
            return String(url).trim();
          }

          return this.fallbackHeroImage || '';
        })
        .filter((url) => url);
    },
    get heroSlideTransform() {
      if (this.heroSlides.length <= 1) {
        return 'translateX(0)';
      }

      return `translateX(-${this.idx * 100}%)`;
    },
    init() {
      if (!Array.isArray(items) || items.length === 0) {
        return;
      }
      if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
        return;
      }
      if (items.length < 2) {
        return;
      }
      this._interval = window.setInterval(() => this.cycle(), 6000);
    },
    destroy() {
      if (this._cyclePending) {
        window.clearTimeout(this._cyclePending);
      }
      if (this._interval) {
        window.clearInterval(this._interval);
      }
    },
    cycle() {
      if (this._cycling || !Array.isArray(this.items) || this.items.length < 2) {
        return;
      }
      this._cycling = true;
      this.visible = false;
      this.idx = (this.idx + 1) % this.items.length;
      if (this._cyclePending) {
        window.clearTimeout(this._cyclePending);
      }
      this._cyclePending = window.setTimeout(() => {
        this.visible = true;
        this._cycling = false;
      }, 520);
    },
  }));
});

window.Alpine = Alpine;
Alpine.start();
