/**
 * Persists company nav logos in the Cache API so they stay instant across
 * full page navigations (not only within a single page load).
 */
const CACHE_NAME = 'litus-nav-company-logos-v1';

/** @type {Map<string, string>} original URL -> blob object URL */
const blobByUrl = new Map();

let warmPromise = null;

function canUseCache() {
  return typeof window !== 'undefined' && 'caches' in window;
}

async function cacheEntryFor(url) {
  const cache = await caches.open(CACHE_NAME);
  let response = await cache.match(url);

  if (!response) {
    response = await fetch(url, { credentials: 'same-origin' });
    if (!response.ok) {
      return null;
    }
    await cache.put(url, response.clone());
  }

  return response;
}

async function blobUrlFor(url) {
  if (blobByUrl.has(url)) {
    return blobByUrl.get(url);
  }

  if (!canUseCache()) {
    return url;
  }

  try {
    const response = await cacheEntryFor(url);
    if (!response) {
      return url;
    }
    const blob = await response.blob();
    const objectUrl = URL.createObjectURL(blob);
    blobByUrl.set(url, objectUrl);
    return objectUrl;
  } catch {
    return url;
  }
}

/**
 * @param {string[]} urls
 */
export async function warmNavLogoCache(urls) {
  const list = Array.isArray(urls)
    ? urls.filter((u) => typeof u === 'string' && u.trim())
    : [];

  if (list.length === 0) {
    return;
  }

  if (!canUseCache()) {
    return;
  }

  await Promise.all(list.map((url) => blobUrlFor(url)));
}

/**
 * Start warming as soon as the navbar markup is parsed (before Alpine boots).
 * @param {string[]} urls
 */
export function scheduleNavLogoWarm(urls) {
  if (warmPromise) {
    return warmPromise;
  }
  warmPromise = warmNavLogoCache(urls);
  return warmPromise;
}

/**
 * Display URL for <img src>: blob URL when cached, otherwise original until warm completes.
 * @param {string} url
 */
export function getNavLogoSrc(url) {
  if (!url || typeof url !== 'string') {
    return '';
  }
  return blobByUrl.get(url) ?? url;
}

if (typeof document !== 'undefined') {
  document.addEventListener('DOMContentLoaded', () => {
    const root = document.querySelector('[data-company-logo-urls]');
    if (!root) {
      return;
    }
    try {
      const urls = JSON.parse(root.getAttribute('data-company-logo-urls') || '[]');
      scheduleNavLogoWarm(urls);
    } catch {
      // ignore invalid JSON
    }
  });
}
