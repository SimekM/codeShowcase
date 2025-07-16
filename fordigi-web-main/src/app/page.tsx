"use client";

import React from "react";

import { HomeHeroSection } from "@/app/components/home/HomeHero";
import { HomeAboutSection } from "@/app/components/home/HomeAbout";
import { HomeProcess } from "@/app/components/home/HomeProcess";
import dynamic from "next/dynamic";

import { TypewriterEffectSmooth } from "@/app/components/ui/TypeWriter";

const ClientServicesSticky = dynamic(
  () => import("@/app/components/home/ClientServicesSticky"),
  { ssr: false }
);

export default function Home() {
  // Service content data for the sticky scroll
  const servicesContent = [

    {
      title: "Tvorba eshopů a webů",
      description: "Vytváříme profesionální weby a e-shopy, které prodávají. Klademe důraz na rychlost, intuitivní ovládání a konverze, které posouvají váš byznys.",
      link: "/services#development",
      image: "/images/placeholder-ecommerce.webp",
      bgGradient: "bg-gradient-to-bl from-secondary/70 to-transparent",
      icon: (
        <svg xmlns="http://www.w3.org/2000/svg" className="h-6 w-6 text-secondary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
        </svg>
      )
    },

    {
      title: "Na míru backend aplikace a systémy",
      description: "Vyvíjíme zakázkové systémy přesně podle vašich potřeb. Propojení s externími systémy, automatizace procesů či administrační rozhraní na klíč.",
      link: "/services#backend",
      image: "/images/placeholder-backend.webp",
      bgGradient: "bg-gradient-to-r from-secondary/80 via-secondary/60 to-transparent",
      icon: (
        <svg xmlns="http://www.w3.org/2000/svg" className="h-8 w-8 text-secondary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4" />
        </svg>
      )
    },
    {
      title: "Správa webu a hosting",
      description: "Poskytujeme spolehlivý hosting a pravidelnou technickou údržbu vašeho webu. Zajistíme aktualizace, zálohy a bezpečnost pro bezproblémový chod.",
      link: "/services#hosting",
      image: "/images/placeholder-hosting.webp",
      bgGradient: "bg-gradient-to-tr from-secondary/70 to-transparent",
      icon: (
        <svg xmlns="http://www.w3.org/2000/svg" className="h-6 w-6 text-secondary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01" />
        </svg>
      )
    },
    {
      title: "SEO optimalizace",
      description: "Zvyšte svou viditelnost ve vyhledávačích. Optimalizujeme váš web pro lepší umístění, analyzujeme klíčová slova a zlepšujeme strukturu pro maximální výkon.",
      link: "/services#seo",
      image: "/images/placeholder-seo.webp",
      bgGradient: "bg-gradient-to-tl from-secondary/70 to-transparent",
      icon: (
        <svg xmlns="http://www.w3.org/2000/svg" className="h-6 w-6 text-secondary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M7 11l5-5m0 0l5 5m-5-5v12" />
        </svg>
      )
    }

  ];

  // Words for the typewriter effect - only for the second line
  const words = [
    {
      text: "weby",
      className: "text-text"
    },
    {
      text: "a",
      className: "text-text"
    },
    {
      text: "další",
      className: "text-text"
    },
    {
      text: "služby.",
      className: "text-text"
    }
  ];

  return (
    <main className="flex flex-col">
      <HomeHeroSection>
        <div className="absolute z-50 inset-0 flex flex-col items-center justify-center px-4 xs:px-8 pointer-events-none">
          <div className="max-w-8xl text-center">
            <h1 className="text-text drop-shadow-2xl leading-[1.15] text-[2rem] xs:text-4xl sm:text-6xl md:text-7xl lg:text-8xl font-bold mb-4 sm:mb-6 break-words hyphens-auto">
              <span className="block">Tvoříme aplikace,</span>
              <span className="block w-full max-w-full overflow-hidden whitespace-nowrap">
                <span className="inline-flex flex-nowrap items-center min-w-0 w-full max-w-full">
                  <TypewriterEffectSmooth
                    words={words}
                    className="font-bold leading-[1.15] inline-flex flex-nowrap min-w-0 max-w-full"
                    cursorClassName="bg-text h-8 xs:h-12 sm:h-16 md:h-24 lg:h-32"
                  />
                </span>
              </span>
            </h1>
            <h3 className="text-text/90 text-sm xs:text-base sm:text-xl md:text-2xl lg:text-3xl mb-6 sm:mb-8 max-w-[280px] xs:max-w-md sm:max-w-2xl md:max-w-4xl mx-auto break-words hyphens-auto">
              Vytvoříme rychlý, funkční a krásný web, nebo vám pomůžeme s automatizací vašeho podnikání.
            </h3>
            <div className="flex gap-3 xs:gap-5 justify-center pointer-events-auto">
              <a href="/services" className="btn btn-shine text-sm xs:text-base">
                <span>Naše služby</span>
              </a>
              <a href="/contact" className="btn btn-outlined btn-shine text-sm xs:text-base">
                <span>Kontakt</span>
                <div className="dots-rise"></div>
              </a>
            </div>
          </div>
        </div>
      </HomeHeroSection>



      {/* Services Section with Sticky Scroll */}
      <ClientServicesSticky content={servicesContent} />

      {/* About Section */}
      <HomeAboutSection />



      <HomeProcess />

    </main>
  );
}
