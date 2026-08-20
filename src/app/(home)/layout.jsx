// NOTE: Do NOT call headers() or cookies() here.
// This layout wraps the home route group. Calling dynamic APIs (headers/cookies)
// inside generateMetadata at this level breaks PPR (Partial Prerender) in Next.js 16
// because it causes a <__next_metadata_boundary__> to be injected into the static shell,
// which then mismatches with the <div> the root layout expects → React error #419.
//
// Dynamic per-locale metadata for the home page is handled inside (home)/page.jsx
// where it is safely inside a Suspense boundary.

export default function HomeLayout({ children }) {
  return children;
}
