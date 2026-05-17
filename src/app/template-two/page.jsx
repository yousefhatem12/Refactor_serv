"use client";
import React from "react";
import WebDevHero from "../components/Services-web-dev/WebDevHero";
import WebDesignFeatures from "../components/Services-web-design/WebDesignFeatures";
import WebDesignPortfolio from "../components/Services-web-design/WebDesignPortfolio";
import WebDevHowWeWork from "../components/Services-web-dev/WebDevHowWeWork";
import WebDesignRequest from "../components/Services-web-design/WebDesignRequest";
import LatestBlogs from "../components/Services-web-hosting/LatestBlogs";

export default function TemplateTwoPage() {
  return (
    <main className="flex min-h-screen flex-col relative overflow-x-hidden">
      <div className="w-full relative z-10 pb-10 pt-10">
        <WebDevHero data={{ title: "Mobile Application Development" }} />
        <WebDesignFeatures title="What distinguish us at Mobile app development?" description=" " />
        <WebDesignPortfolio />
        <WebDevHowWeWork />
        <WebDesignRequest />
        <LatestBlogs />
      </div>
    </main>
  );
}
