import './bootstrap';

import Alpine from 'alpinejs';
import intersect from '@alpinejs/intersect';

Alpine.plugin(intersect);

/** Home hero “Featured Company” — matches HomePage.tsx AnimatePresence mode="wait" (exit then enter, 500ms). */
document.addEventListener('alpine:init', () => {
  /** Careers — matches Careers.tsx useInView(once, margin -100px); skip animation if reduced motion. */
  Alpine.data('careersPage', () => ({
    careersInView: false,
    init() {
      if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
        this.careersInView = true;
      }
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

  Alpine.data('heroSpotlight', (items) => ({
    items,
    idx: 0,
    visible: true,
    _interval: null,
    init() {
      if (!Array.isArray(items) || items.length === 0) {
        return;
      }
      if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
        return;
      }
      this._interval = window.setInterval(() => this.cycle(), 3000);
    },
    destroy() {
      if (this._interval) {
        window.clearInterval(this._interval);
      }
    },
    cycle() {
      this.visible = false;
      window.setTimeout(() => {
        this.idx = (this.idx + 1) % this.items.length;
        this.visible = true;
      }, 500);
    },
  }));
});

window.Alpine = Alpine;
Alpine.start();
