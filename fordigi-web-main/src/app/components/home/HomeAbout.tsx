"use client";

import React, { useRef } from "react";
import { motion, useInView, useScroll, useTransform } from "motion/react";
import Image from "next/image";
import { cn } from "@/lib/utils";

export const HomeAboutSection = () => {
    const sectionRef = useRef<HTMLDivElement>(null);
    const containerRef = useRef<HTMLDivElement>(null);
    const isInView = useInView(sectionRef, { once: true, margin: "-100px" });

    // Scroll-based animations
    const { scrollYProgress } = useScroll({
        target: containerRef,
        offset: ["start end", "end start"]
    });

    const y1 = useTransform(scrollYProgress, [0, 1], [0, -50]);
    const y2 = useTransform(scrollYProgress, [0, 1], [0, 100]);

    const stats = [
        { value: "5+", label: "Let zkušeností v oboru" },
        { value: "24/7", label: "Technická podpora" },
        { value: "100%", label: "Záruka kvality práce" },
    ];

    return (
        <section
            ref={sectionRef}
            id="onas"
            className="relative py-24 md:py-32 lg:py-40 overflow-hidden bg-gradient-to-b from-blue-50/30 via-slate-50 to-primary"
        >
            {/* Enhanced background elements */}
            <div className="absolute inset-0 opacity-30 z-0">
                <motion.div
                    className="absolute top-1/4 left-1/6 w-96 h-96 bg-secondary/10 rounded-full blur-[100px]"
                    animate={{
                        scale: [1, 1.2, 1],
                        opacity: [0.3, 0.5, 0.3]
                    }}
                    transition={{
                        duration: 8,
                        repeat: Infinity,
                        ease: "easeInOut"
                    }}
                />
                <motion.div
                    className="absolute bottom-1/4 right-1/6 w-80 h-80 bg-text/5 rounded-full blur-[80px]"
                    animate={{
                        scale: [1.2, 1, 1.2],
                        opacity: [0.2, 0.4, 0.2]
                    }}
                    transition={{
                        duration: 10,
                        repeat: Infinity,
                        ease: "easeInOut",
                        delay: 2
                    }}
                />
            </div>

            {/* Floating geometric shapes */}
            {[...Array(8)].map((_, i) => (
                <motion.div
                    key={`shape-${i}`}
                    className="absolute pointer-events-none z-10"
                    style={{
                        left: `${Math.random() * 100}%`,
                        top: `${20 + Math.random() * 60}%`,
                    }}
                    animate={{
                        y: [0, -40, 0],
                        x: [0, Math.random() * 30 - 15, 0],
                        rotate: [0, Math.random() * 360, 0],
                        opacity: [0.1, 0.3, 0.1],
                    }}
                    transition={{
                        duration: 8 + Math.random() * 4,
                        repeat: Infinity,
                        delay: Math.random() * 4,
                        ease: "easeInOut"
                    }}
                >
                    <div className={`w-4 h-4 ${i % 3 === 0 ? 'bg-secondary/20' : i % 3 === 1 ? 'bg-text/20' : 'bg-secondary/15'} ${i % 2 === 0 ? 'rounded-full' : 'rounded-sm rotate-45'}`} />
                </motion.div>
            ))}

            <motion.div
                ref={containerRef}
                className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-20"
                initial={{ opacity: 0 }}
                animate={{ opacity: isInView ? 1 : 0 }}
                transition={{ duration: 1.2 }}
            >
                {/* Enhanced Header */}
                <motion.div
                    initial={{ opacity: 0, y: 60 }}
                    animate={{ opacity: isInView ? 1 : 0, y: isInView ? 0 : 60 }}
                    transition={{ duration: 1.2, ease: "easeOut" }}
                    className="mb-20 text-center"
                >
                    <motion.div className="flex items-center justify-center gap-6 mb-8">
                        <motion.div
                            className="h-px bg-gradient-to-r from-transparent via-secondary to-secondary flex-1 max-w-xs"
                            initial={{ scaleX: 0 }}
                            animate={{ scaleX: isInView ? 1 : 0 }}
                            transition={{ duration: 1.5, delay: 0.5 }}
                        />
                        <motion.span
                            className="text-secondary text-sm font-mono uppercase tracking-wider font-bold px-4 py-2 bg-white/80 backdrop-blur-sm rounded-full border border-secondary/20"
                            initial={{ scale: 0 }}
                            animate={{ scale: isInView ? 1 : 0 }}
                            transition={{ duration: 0.8, delay: 0.8 }}
                        >
                            Náš příběh
                        </motion.span>
                        <motion.div
                            className="h-px bg-gradient-to-l from-transparent via-secondary to-secondary flex-1 max-w-xs"
                            initial={{ scaleX: 0 }}
                            animate={{ scaleX: isInView ? 1 : 0 }}
                            transition={{ duration: 1.5, delay: 0.5 }}
                        />
                    </motion.div>

                    <motion.h2
                        className="text-5xl md:text-6xl lg:text-7xl font-bold text-text mb-8 leading-tight"
                        initial={{ opacity: 0, y: 30 }}
                        animate={{ opacity: isInView ? 1 : 0, y: isInView ? 0 : 30 }}
                        transition={{ duration: 1, delay: 1 }}
                    >
                        Kdo jsme <span className="text-secondary">a co</span><br />
                        <span className="text-secondary">děláme</span>
                    </motion.h2>
                </motion.div>

                {/* Main content grid */}
                <div className="grid lg:grid-cols-2 gap-20 lg:gap-28 items-center">
                    {/* Left side - Content */}
                    <motion.div
                        className="space-y-8"
                        initial={{ opacity: 0, x: -60 }}
                        animate={{ opacity: isInView ? 1 : 0, x: isInView ? 0 : -60 }}
                        transition={{ duration: 1.2, delay: 0.4 }}
                    >
                        <motion.div
                            initial={{ opacity: 0, y: 40 }}
                            animate={{ opacity: isInView ? 1 : 0, y: isInView ? 0 : 40 }}
                            transition={{ duration: 0.8, delay: 0.6 }}
                            className="space-y-6"
                        >
                            <p className="text-xl text-text/90 leading-relaxed">
                                Jsme tým zkušených vývojářů a designérů, kteří se specializují na tvorbu moderních webových řešení. Naším cílem je pomáhat firmám růst prostřednictvím kvalitních webových stránek a aplikací.
                            </p>
                            <p className="text-lg text-text/80 leading-relaxed">
                                S našimi klienty budujeme dlouhodobé vztahy založené na důvěře a výsledcích. Každý projekt je pro nás výzvou, jak posunout vaše podnikání na další úroveň.
                            </p>
                        </motion.div>

                        {/* Enhanced Stats */}
                        <motion.div
                            className="grid grid-cols-1 xs:grid-cols-2 lg:grid-cols-3 gap-3 xs:gap-4 sm:gap-6"
                            initial={{ opacity: 0, y: 40 }}
                            animate={{ opacity: isInView ? 1 : 0, y: isInView ? 0 : 40 }}
                            transition={{ duration: 0.8, delay: 0.8 }}
                        >
                            {stats.map((stat, index) => (
                                <motion.div
                                    key={index}
                                    initial={{ opacity: 0, scale: 0.8, y: 20 }}
                                    animate={{ opacity: isInView ? 1 : 0, scale: isInView ? 1 : 0.8, y: isInView ? 0 : 20 }}
                                    transition={{
                                        duration: 0.8,
                                        delay: index * 0.15 + 1,
                                        type: "spring",
                                        damping: 20,
                                        stiffness: 100
                                    }}
                                    className={cn(
                                        "text-center p-3 xs:p-4 sm:p-6 border-2 border-text/10 rounded-2xl bg-white/95 backdrop-blur-lg",
                                        "hover:border-secondary/30 hover:bg-white hover:shadow-xl hover:shadow-secondary/5 transition-all duration-500 group",
                                        // Make the last item span full width on mobile if there's an odd number of items
                                        stats.length % 2 !== 0 && index === stats.length - 1 ? "xs:col-span-2 lg:col-span-1" : ""
                                    )}
                                    whileHover={{
                                        scale: 1.05,
                                        y: -5,
                                    }}
                                >
                                    <motion.div
                                        className="text-2xl xs:text-3xl sm:text-4xl font-bold text-text mb-1 xs:mb-2 group-hover:text-secondary transition-colors duration-300"
                                        whileHover={{ scale: 1.05 }}
                                        transition={{ type: "spring", damping: 15 }}
                                    >
                                        {stat.value}
                                    </motion.div>
                                    <div className="text-text/70 text-xs xs:text-sm leading-tight group-hover:text-text/90 transition-colors duration-300 font-medium">
                                        {stat.label}
                                    </div>
                                </motion.div>
                            ))}
                        </motion.div>

                        {/* Enhanced CTA */}
                        {/* <motion.div
                            initial={{ opacity: 0, y: 20 }}
                            animate={{ opacity: isInView ? 1 : 0, y: isInView ? 0 : 20 }}
                            transition={{ duration: 0.8, delay: 1.2 }}
                            className="pt-4"
                        >
                            <motion.a 
                                href="/about" 
                                className="btn btn-outlined btn-shine inline-flex items-center gap-2"
                                whileHover={{ scale: 1.05 }}
                                whileTap={{ scale: 0.95 }}
                            >
                                <span>Více o nás</span>
                                <motion.svg 
                                    xmlns="http://www.w3.org/2000/svg" 
                                    className="h-4 w-4" 
                                    fill="none" 
                                    viewBox="0 0 24 24" 
                                    stroke="currentColor"
                                    animate={{ x: [0, 3, 0] }}
                                    transition={{ duration: 2, repeat: Infinity, ease: "easeInOut" }}
                                >
                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M17 8l4 4m0 0l-4 4m4-4H3" />
                                </motion.svg>
                            </motion.a>
                        </motion.div> */}
                    </motion.div>

                    {/* Right side - Enhanced Image */}
                    <motion.div
                        className="relative"
                        initial={{ opacity: 0, x: 60 }}
                        animate={{ opacity: isInView ? 1 : 0, x: isInView ? 0 : 60 }}
                        transition={{ duration: 1.2, delay: 0.6 }}
                        style={{ y: y1 }}
                    >
                        {/* Main image container */}
                        <motion.div
                            className="relative rounded-3xl overflow-hidden shadow-2xl border-2 border-text/5 bg-white/95 backdrop-blur-lg"
                            whileHover={{
                                scale: 1.02,
                                y: -10,
                            }}
                            transition={{ type: "spring", damping: 20, stiffness: 300 }}
                        >
                            <Image
                                src="/images/about-image.jpg"
                                alt="Náš tým při práci"
                                width={600}
                                height={400}
                                className="w-full h-auto object-cover aspect-[4/3]"
                            />

                            {/* Overlay gradient */}
                            <div className="absolute inset-0 bg-gradient-to-t from-secondary/10 via-transparent to-transparent" />
                        </motion.div>

                        {/* Floating decorative elements */}
                        <motion.div
                            className="absolute -bottom-6 -right-6 w-32 h-32 border-4 border-secondary/20 rounded-2xl -z-10 bg-white/50 backdrop-blur-sm"
                            animate={{
                                rotate: [0, 5, -5, 0],
                                scale: [1, 1.05, 1]
                            }}
                            transition={{
                                duration: 6,
                                repeat: Infinity,
                                ease: "easeInOut"
                            }}
                            style={{ y: y2 }}
                        />

                        <motion.div
                            className="absolute -top-4 -left-4 w-24 h-24 bg-secondary/10 rounded-full backdrop-blur-sm"
                            animate={{
                                scale: [1, 1.2, 1],
                                opacity: [0.3, 0.6, 0.3]
                            }}
                            transition={{
                                duration: 4,
                                repeat: Infinity,
                                ease: "easeInOut",
                                delay: 1
                            }}
                        />

                        {/* Accent line */}
                        <motion.div
                            className="absolute -bottom-12 left-1/2 transform -translate-x-1/2 w-48 h-1 bg-gradient-to-r from-transparent via-secondary to-transparent rounded-full"
                            initial={{ scaleX: 0 }}
                            animate={{ scaleX: isInView ? 1 : 0 }}
                            transition={{ duration: 1.5, delay: 1.5 }}
                        />
                    </motion.div>
                </div>
            </motion.div>
        </section>
    );
};
