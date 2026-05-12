const API_BASE_URL = process.env.NEXT_PUBLIC_API_URL || 'http://127.0.0.1:8000/api';

export const getMediaUrl = (path) => {
  if (!path) return '';
  if (path.startsWith('http')) return path;
  
  const baseUrl = API_BASE_URL.replace(/\/api$/, '');
  return `${baseUrl}/storage/${path.replace(/^\//, '')}`;
};

export const fetchAPI = async (endpoint, options = {}) => {
  let lang = 'en';
  if (typeof document !== 'undefined') {
    const match = document.cookie.match(new RegExp('(^| )NEXT_LOCALE=([^;]+)'));
    if (match) lang = match[2];
  }

  const url = new URL(`${API_BASE_URL}${endpoint}`);
  url.searchParams.append('lang', lang);

  const token = typeof window !== 'undefined' ? localStorage.getItem('auth_token') : null;

  try {
    const response = await fetch(url.toString(), {
      ...options,
      cache: options.cache || 'no-store',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'Accept-Language': lang,
        ...(token ? { 'Authorization': `Bearer ${token}` } : {}),
        ...options.headers,
      },
    });

    if (!response.ok) {
      console.warn(`API warning: ${response.statusText}`);
      return null;
    }

    return await response.json();
  } catch (error) {
    console.warn('API connection failed:', error.message);
    return null; // Return null so the UI doesn't crash
  }
};
