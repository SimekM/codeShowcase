"use client";
import React from "react";
import { motion } from "framer-motion";
import Image from "next/image";

export const HomeProcess = () => {
  // Process content data
  const processContent = [
    {
      title: "Analýza",
      description: "Začínáme důkladnou analýzou vašich potřeb, cílů a stávajících řešení. Identifikujeme klíčové oblasti pro zlepšení a sestavíme strategii, která bude přesně odpovídat vašim požadavkům a rozpočtu.",
      link: "/process#analysis",
      image: "/images/placeholder-analysis.webp",
      bgGradient: "bg-gradient-to-br from-secondary/70 to-transparent",
      icon: (
        <svg xmlns="http://www.w3.org/2000/svg" className="h-6 w-6 text-secondary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
        </svg>
      )
    },
    {
      title: "Návrh",
      description: "Vytváříme detailní návrh řešení, který zahrnuje wireframy, design a architekturu systému. Důraz klademe na uživatelskou přívětivost, moderní design a optimální technickou strukturu, která zajistí dlouhodobou funkčnost.",
      link: "/process#design",
      image: "/images/placeholder-design.webp",
      bgGradient: "bg-gradient-to-bl from-secondary/70 to-transparent",
      icon: (
        <svg xmlns="http://www.w3.org/2000/svg" className="h-6 w-6 text-secondary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
        </svg>
      )
    },
    {
      title: "Realizace",
      description: "V této fázi přetváříme návrhy v realitu. Náš zkušený tým vývojářů pracuje na implementaci řešení podle stanovených specifikací. Používáme nejmodernější technologie a postupy pro zajištění kvality a efektivity.",
      link: "/process#implementation",
      image: "/images/placeholder-implementation.webp",
      bgGradient: "bg-gradient-to-tr from-secondary/70 to-transparent",
      icon: (
        <svg xmlns="http://www.w3.org/2000/svg" className="h-6 w-6 text-secondary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4" />
        </svg>
      )
    },
    {
      title: "Optimalizace",
      description: "Po spuštění projektu neustále sledujeme jeho výkon a sbíráme zpětnou vazbu. Na základě dat a vašich požadavků průběžně optimalizujeme a vylepšujeme řešení pro dosažení maximálních výsledků a spokojenosti uživatelů.",
      link: "/process#optimization",
      image: "/images/placeholder-optimization.webp",
      bgGradient: "bg-gradient-to-tl from-secondary/70 to-transparent",
      icon: (
        <svg xmlns="http://www.w3.org/2000/svg" className="h-6 w-6 text-secondary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M13 10V3L4 14h7v7l9-11h-7z" />
        </svg>
      )
    }
  ];

  return (
    <section className="w-full py-20 bg-gradient-to-br from-primary-variant/20 via-primary/95 to-primary overflow-hidden">
      <div className="max-w-7xl mx-auto px-4 sm:px-6">
        {/* Section Header with enhanced animation */}
        <motion.div
          initial={{ opacity: 0, y: 20 }}
          whileInView={{ opacity: 1, y: 0 }}
          viewport={{ once: true }}
          transition={{ duration: 0.6 }}
          className="text-center mb-16"
        >
          <motion.span
            initial={{ opacity: 0, scale: 0.5 }}
            whileInView={{ opacity: 1, scale: 1 }}
            viewport={{ once: true }}
            transition={{ duration: 0.4 }}
            className="inline-block text-sm font-semibold text-secondary bg-secondary/10 py-1 px-3 rounded-full mb-4"
          >
            Náš proces
          </motion.span>
          <h2 className="text-4xl md:text-5xl font-bold mb-6 text-text">
            Jak <span className="text-secondary relative inline-block">
              postupujeme
              <motion.svg
                initial={{ pathLength: 0, opacity: 0 }}
                whileInView={{ pathLength: 1, opacity: 1 }}
                viewport={{ once: true }}
                transition={{ duration: 1, ease: "easeInOut" }}
                className="absolute -bottom-2 left-0 w-full"
                height="6"
                viewBox="0 0 100 6"
                preserveAspectRatio="none"
              >
                <motion.path
                  d="M0,5 Q20,3 35,5 Q50,8 65,5 Q80,3 100,5"
                  stroke="rgba(15, 60, 120, 0.3)"
                  strokeWidth="6"
                  fill="none"
                  strokeLinecap="round"
                />
              </motion.svg>
            </span>
          </h2>
          <motion.p
            initial={{ opacity: 0, y: 10 }}
            whileInView={{ opacity: 1, y: 0 }}
            viewport={{ once: true }}
            transition={{ duration: 0.6, delay: 0.2 }}
            className="text-lg text-text/70 max-w-2xl mx-auto"
          >
            Každý projekt je jedinečný, ale náš přístup zajišťuje konzistentní výsledky. Podívejte se na náš osvědčený proces vývoje.
          </motion.p>
        </motion.div>

        {/* Process Cards */}
        <div className="relative">
          {/* Enhanced Connecting Line */}
          <div className="absolute left-1/2 top-0 bottom-0 w-px bg-secondary/20 transform -translate-x-1/2 hidden lg:block">
            <motion.div
              initial={{ height: "0%" }}
              whileInView={{ height: "100%" }}
              viewport={{ once: true, margin: "-100px" }}
              transition={{ duration: 1.5, ease: "easeInOut" }}
              className="absolute top-0 left-0 w-full bg-secondary/40"
              style={{ transformOrigin: "top" }}
            />
          </div>

          {/* Process Steps */}
          <div className="space-y-12 relative">
            {processContent.map((item, index) => (
              <motion.div
                key={index}
                initial={{ opacity: 0, y: 40 }}
                whileInView={{ opacity: 1, y: 0 }}
                viewport={{ once: true, margin: "-100px" }}
                transition={{ duration: 0.8, delay: index * 0.2 }}
                className={`flex flex-col lg:flex-row gap-8 items-center ${index % 2 === 0 ? 'lg:flex-row' : 'lg:flex-row-reverse'
                  }`}
              >
                {/* Content Side */}
                <div className={`w-full lg:w-1/2 ${index % 2 === 0 ? 'lg:pr-12' : 'lg:pl-12'}`}>
                  <div className="relative">
                    {/* Enhanced Step Number */}
                    <motion.div
                      initial={{ scale: 0, rotate: -180 }}
                      whileInView={{ scale: 1, rotate: 0 }}
                      viewport={{ once: true }}
                      transition={{ duration: 0.6, delay: index * 0.1 }}
                      className="z-10 absolute -left-4 -top-4 w-12 h-12 rounded-full bg-secondary/10 flex items-center justify-center"
                    >
                      <span className="text-secondary font-bold text-lg">{index + 1}</span>
                      <motion.div
                        className="absolute inset-0 rounded-full border-2 border-secondary/30"
                        initial={{ scale: 0.8, opacity: 0 }}
                        whileInView={{ scale: 1, opacity: 1 }}
                        viewport={{ once: true }}
                        transition={{ duration: 0.3, delay: index * 0.1 + 0.3 }}
                      />
                    </motion.div>

                    {/* Enhanced Card */}
                    <motion.div
                      initial={{ x: index % 2 === 0 ? -20 : 20, opacity: 0 }}
                      whileInView={{ x: 0, opacity: 1 }}
                      viewport={{ once: true }}
                      transition={{ duration: 0.6, delay: index * 0.2 }}
                      whileHover={{ scale: 1.02 }}
                      className="bg-white/50 backdrop-blur-sm rounded-2xl p-8 shadow-lg hover:shadow-xl transition-all duration-300"
                    >
                      <motion.div
                        initial={{ y: 10, opacity: 0 }}
                        whileInView={{ y: 0, opacity: 1 }}
                        viewport={{ once: true }}
                        transition={{ duration: 0.4, delay: index * 0.2 + 0.2 }}
                        className="flex items-center gap-4 mb-4"
                      >
                        <motion.div
                          whileHover={{ rotate: 360 }}
                          transition={{ duration: 0.6 }}
                          className="bg-secondary/10 rounded-full p-3"
                        >
                          {item.icon}
                        </motion.div>
                        <h3 className="text-2xl font-bold text-text">{item.title}</h3>
                      </motion.div>

                      <motion.div
                        initial={{ width: 0 }}
                        whileInView={{ width: "4rem" }}
                        viewport={{ once: true }}
                        transition={{ duration: 0.6, delay: index * 0.2 + 0.3 }}
                        className="h-1 bg-secondary/30 rounded-full mb-4"
                      />

                      <motion.p
                        initial={{ y: 10, opacity: 0 }}
                        whileInView={{ y: 0, opacity: 1 }}
                        viewport={{ once: true }}
                        transition={{ duration: 0.4, delay: index * 0.2 + 0.4 }}
                        className="text-text/70 leading-relaxed"
                      >
                        {item.description}
                      </motion.p>
                    </motion.div>
                  </div>
                </div>

                {/* Image Side with enhanced animations */}
                <motion.div
                  initial={{ x: index % 2 === 0 ? 20 : -20, opacity: 0 }}
                  whileInView={{ x: 0, opacity: 1 }}
                  viewport={{ once: true }}
                  transition={{ duration: 0.6, delay: index * 0.2 }}
                  className="w-full lg:w-1/2"
                >
                  <div className="relative rounded-2xl overflow-hidden aspect-[4/3] shadow-xl group">
                    <Image
                      src={item.image}
                      alt={item.title}
                      fill
                      className="object-cover transform group-hover:scale-105 transition-transform duration-700"
                      sizes="(max-width: 768px) 100vw, 50vw"
                      quality={90}
                    />
                    <motion.div
                      initial={{ opacity: 0 }}
                      whileInView={{ opacity: 0.6 }}
                      viewport={{ once: true }}
                      transition={{ duration: 0.6, delay: index * 0.2 + 0.3 }}
                      className={`absolute inset-0 ${item.bgGradient}`}
                    />

                    {/* Enhanced Decorative Elements */}
                    <motion.div
                      initial={{ scale: 0, rotate: -180 }}
                      whileInView={{ scale: 1, rotate: 0 }}
                      viewport={{ once: true }}
                      transition={{ duration: 0.6, delay: index * 0.2 + 0.4 }}
                      className="absolute top-6 right-6 bg-white/20 backdrop-blur-sm rounded-full p-3"
                    >
                      {item.icon}
                    </motion.div>
                  </div>
                </motion.div>
              </motion.div>
            ))}
          </div>
        </div>
      </div>
    </section>
  );
};
