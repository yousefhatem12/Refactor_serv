import React from "react";

import WebDesignHero from "../components/Services-web-design/WebDesignHero";
import WebDesignFeatures from "../components/Services-web-design/WebDesignFeatures";
import WebDesignPortfolio from "../components/Services-web-design/WebDesignPortfolio";
import WebDesignRequest from "../components/Services-web-design/WebDesignRequest";
import LatestBlogs from "../components/Services-web-hosting/LatestBlogs";

export const metadata = {
  title: "Web Design Services | Serv5",
  description: "Get the best web design services for your business.",
};

export default function WebDesignPage() {
  return (
    <main className="flex min-h-screen flex-col relative overflow-x-hidden">
      <div className="w-full relative z-10 pb-10 pt-20">
        <WebDesignHero />
        <WebDesignFeatures />
        <WebDesignPortfolio />
        <WebDesignRequest />
        <LatestBlogs />
      </div>
    </main>
  );
}
