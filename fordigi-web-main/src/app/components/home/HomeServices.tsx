"use client";

import React, { useRef, useState, useEffect } from "react";
import { motion, useInView, useScroll, useTransform } from "motion/react";
import { cn } from "@/app/utils/cn";
import { ServicesGridProps } from "./types";

export const ServicesSticky = ({ content }: ServicesGridProps) => {
  const [hoveredCard, setHoveredCard] = useState<number | null>(null);
  const sectionRef = useRef<HTMLDivElement>(null);
  const containerRef = useRef<HTMLDivElement>(null);
  const isInView = useInView(sectionRef, { once: true, margin: "-100px" });
  const [isMobile, setIsMobile] = useState(false);

  // Check for mobile on mount and window resize
  useEffect(() => {
    const checkMobile = () => {
      setIsMobile(window.innerWidth < 768); // md breakpoint
    };

    checkMobile();
    window.addEventListener('resize', checkMobile);

    return () => {
      window.removeEventListener('resize', checkMobile);
    };
  }, []);

  // Pre-generate random positions with mobile-aware quantities
  const binaryPositions = React.useMemo(() =>
    [...Array(isMobile ? 10 : 25)].map(() => ({
      left: Math.random() * 100,
      top: 10 + Math.random() * 80,
      moveX: Math.random() * 60 - 30,
      moveY: -80,
      rotation: Math.random() * 720 - 360,
      duration: 12 + Math.random() * 8,
      delay: Math.random() * 6,
      bit: Math.random() > 0.5 ? '1' : '0'
    })), [isMobile]
  );

  const symbolPositions = React.useMemo(() =>
    ['{', '}', '<', '>', '/', '=', ';', '(', ')', '[', ']', '+', '-', '*', '&&', '||', '==', '!=', '=>', '++', '--']
      .slice(0, isMobile ? 8 : 21) // Show fewer symbols on mobile
      .map((symbol) => ({
        symbol,
        left: Math.random() * 100,
        top: 15 + Math.random() * 70,
        moveX: Math.random() * 80 - 40,
        moveY: -100,
        rotation: Math.random() * 360,
        rotationY: Math.random() * 180,
        duration: 15 + Math.random() * 10,
        delay: Math.random() * 8
      })), [isMobile]
  );

  const binary3DPositions = React.useMemo(() =>
    [...Array(isMobile ? 15 : 30)].map(() => ({
      left: Math.random() * 600,
      top: Math.random() * 600,
      z: Math.random() * 50 + 10,
      duration: 3 + Math.random() * 2,
      delay: Math.random() * 2,
      bit: Math.random() > 0.5 ? '1' : '0'
    })), [isMobile]
  );

  // Scroll-based animations
  const { scrollYProgress } = useScroll({
    target: containerRef,
    offset: ["start end", "end start"]
  });

  const y1 = useTransform(scrollYProgress, [0, 1], [0, -50]);

  // Statistics data
  const stats = [
    { number: "50+", label: "realizovaných webových projektů" },
    { number: "100%", label: "nasazení do každé zakázky" },
    { number: "10+", label: "let zkušeností v oboru" },
    { number: "∞", label: "řádků kódu" }
  ];


  return (
    <section
      ref={sectionRef}
      id="sluzby"
      className="relative  overflow-hidden bg-gradient-to-b from-primary via-slate-50 to-blue-50/30"
    >
      {/* Smooth transition overlay from hero */}
      <div className="absolute top-0 left-0 w-full h-40 bg-gradient-to-b from-primary via-primary/80 to-transparent z-20"></div>

      {/* Code background pattern overlay */}
      <div className="absolute inset-0 opacity-5 z-0">
        <div className="absolute inset-0 font-mono text-xs text-secondary leading-relaxed whitespace-pre-wrap px-8">

        </div>
      </div>

      {/* Binary Numbers with adjusted opacity and size for mobile */}
      {binaryPositions.map((pos) => (
        <motion.div
          key={`section-bit-${pos.left}-${pos.top}`}
          className={cn(
            "absolute font-mono font-bold pointer-events-none z-50",
            isMobile ? "text-base text-secondary/40" : "text-lg text-secondary/60"
          )}
          style={{
            left: `${pos.left}%`,
            top: `${pos.top}%`,
          }}
          animate={{
            y: [0, pos.moveY, 0],
            x: [0, pos.moveX, 0],
            opacity: [0.3, 0.6, 0.3],
            scale: isMobile ? [0.4, 1, 0.4] : [0.6, 1.4, 0.6],
            rotate: [0, pos.rotation, 0]
          }}
          transition={{
            duration: pos.duration,
            repeat: Infinity,
            delay: pos.delay,
            ease: "easeInOut"
          }}
        >
          {pos.bit}
        </motion.div>
      ))}

      {/* Floating Code Symbols with adjusted opacity and size for mobile */}
      {symbolPositions.map((pos) => (
        <motion.div
          key={`section-symbol-${pos.symbol}-${pos.left}-${pos.top}`}
          className={cn(
            "absolute font-mono font-bold pointer-events-none z-50",
            isMobile ? "text-xl text-secondary/40" : "text-2xl text-secondary/50"
          )}
          style={{
            left: `${pos.left}%`,
            top: `${pos.top}%`,
          }}
          animate={{
            y: [0, pos.moveY, 0],
            x: [0, pos.moveX, 0],
            opacity: [0.2, 0.5, 0.2],
            scale: isMobile ? [0.4, 1.2, 0.4] : [0.5, 1.6, 0.5],
            rotateZ: [0, pos.rotation, 0],
            rotateY: [0, pos.rotationY, 0]
          }}
          transition={{
            duration: pos.duration,
            repeat: Infinity,
            delay: pos.delay,
            ease: "easeInOut"
          }}
        >
          {pos.symbol}
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
          className="mb-12 sm:mb-16 lg:mb-20 text-center"
        >
          <motion.div className="flex items-center justify-center gap-4 sm:gap-6 mb-6 sm:mb-8">
            <motion.div
              className="h-px bg-gradient-to-r from-transparent via-secondary to-secondary flex-1 max-w-[100px] sm:max-w-xs"
              initial={{ scaleX: 0 }}
              animate={{ scaleX: isInView ? 1 : 0 }}
              transition={{ duration: 1.5, delay: 0.5 }}
            />

            <motion.div
              className="h-px bg-gradient-to-l from-transparent via-secondary to-secondary flex-1 max-w-[100px] sm:max-w-xs"
              initial={{ scaleX: 0 }}
              animate={{ scaleX: isInView ? 1 : 0 }}
              transition={{ duration: 1.5, delay: 0.5 }}
            />
          </motion.div>

          <motion.h2
            className="text-3xl sm:text-4xl md:text-5xl lg:text-6xl xl:text-7xl font-bold text-text mb-6 sm:mb-8 leading-tight"
            initial={{ opacity: 0, y: 30 }}
            animate={{ opacity: isInView ? 1 : 0, y: isInView ? 0 : 30 }}
            transition={{ duration: 1, delay: 1 }}
          >
            Co všechno děláme?
          </motion.h2>
        </motion.div>

        {/* Enhanced main content grid */}
        <div className="grid gap-10 sm:gap-12 md:gap-16 lg:grid-cols-2 lg:gap-20 xl:gap-28">
          {/* Left side - Services */}
          <motion.div
            className="space-y-3 sm:space-y-4"
            initial={{ opacity: 0, x: -60 }}
            animate={{ opacity: isInView ? 1 : 0, x: isInView ? 0 : -60 }}
            transition={{ duration: 1.2, delay: 0.4 }}
          >
            {content.map((item, index) => (
              <motion.div
                key={index}
                initial={{ opacity: 0, y: 40 }}
                animate={{ opacity: isInView ? 1 : 0, y: isInView ? 0 : 40 }}
                transition={{
                  duration: 0.8,
                  delay: index * 0.15 + 0.6,
                  type: "spring",
                  damping: 25,
                  stiffness: 120
                }}
                className="group cursor-pointer"
                onMouseEnter={() => setHoveredCard(index)}
                onMouseLeave={() => setHoveredCard(null)}
              >
                <motion.div
                  className="border-l-4 border-text/30 hover:border-secondary pl-4 sm:pl-6 py-4 sm:py-6 transition-all duration-500 bg-white/90 backdrop-blur-lg rounded-r-2xl hover:bg-white hover:shadow-xl hover:shadow-secondary/5 relative overflow-hidden"
                  whileHover={{
                    scale: 1.01,
                    x: 5,
                  }}
                  transition={{ type: "spring", damping: 20, stiffness: 300 }}
                >
                  {/* Service icon */}
                  <motion.div
                    className="mb-2 sm:mb-3"
                    whileHover={{ scale: 1.05 }}
                    transition={{ type: "spring", damping: 15 }}
                  >
                    {item.icon}
                  </motion.div>

                  {/* Service title */}
                  <motion.h3
                    className="text-lg sm:text-xl lg:text-2xl font-bold text-secondary mb-2 sm:mb-3 group-hover:text-secondary-variant transition-colors duration-300"
                    layoutId={`title-${index}`}
                  >
                    {item.title}
                  </motion.h3>

                  {/* Service description */}
                  <motion.p
                    className="text-text/80 text-sm sm:text-base leading-relaxed mb-3 sm:mb-4 group-hover:text-text transition-colors duration-300"
                  >
                    {item.description}
                  </motion.p>

                  {/* CTA Button */}
                  <motion.div className="pt-1">
                    <motion.a
                      href={item.link}
                      className="btn btn-sm btn-shine inline-flex items-center gap-2 text-sm"
                      whileHover={{ scale: 1.05 }}
                      whileTap={{ scale: 0.95 }}
                    >
                      <span>Více</span>
                      <motion.svg
                        xmlns="http://www.w3.org/2000/svg"
                        className="h-4 w-4"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                        animate={{ x: hoveredCard === index ? 3 : 0 }}
                        transition={{ duration: 0.3 }}
                      >
                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M17 8l4 4m0 0l-4 4m4-4H3" />
                      </motion.svg>
                    </motion.a>
                  </motion.div>
                </motion.div>
              </motion.div>
            ))}
          </motion.div>

          {/* Right side - Statistics and 3D Object */}
          <motion.div
            className="relative flex flex-col gap-5"
            initial={{ opacity: 0, x: 60 }}
            animate={{ opacity: isInView ? 1 : 0, x: isInView ? 0 : 60 }}
            transition={{ duration: 1.2, delay: 0.6 }}
          >
            {/* Statistics grid */}
            <div className="grid grid-cols-2 gap-2 xs:gap-3 sm:gap-4 md:gap-6 relative z-10 mb-4 sm:mb-6 md:mb-8">
              {stats.map((stat, index) => (
                <motion.div
                  key={index}
                  initial={{ opacity: 0, scale: 0.8, y: 20 }}
                  animate={{ opacity: isInView ? 1 : 0, scale: isInView ? 1 : 0.8, y: isInView ? 0 : 20 }}
                  transition={{
                    duration: 0.8,
                    delay: index * 0.2 + 1,
                    type: "spring",
                    damping: 20,
                    stiffness: 100
                  }}
                  className="text-center p-2 xs:p-3 sm:p-4 md:p-6 border-2 border-text/10 rounded-xl sm:rounded-2xl bg-white/95 backdrop-blur-lg hover:border-secondary/30 hover:bg-white hover:shadow-xl hover:shadow-secondary/5 transition-all duration-500 group"
                  whileHover={{
                    scale: 1.03,
                    y: -3,
                  }}
                >
                  <motion.div
                    className="text-xl xs:text-2xl sm:text-3xl md:text-4xl lg:text-5xl font-bold text-text mb-0.5 xs:mb-1 sm:mb-2 group-hover:text-secondary transition-colors duration-300"
                    whileHover={{ scale: 1.05 }}
                    transition={{ type: "spring", damping: 15 }}
                  >
                    {stat.number}
                  </motion.div>
                  <div className="text-text/70 text-[10px] xs:text-xs sm:text-sm leading-tight group-hover:text-text/90 transition-colors duration-300 font-medium">
                    {stat.label}
                  </div>
                </motion.div>
              ))}
            </div>

            {/* 3D Coding Scene - Under stats */}
            <motion.div
              className="relative h-[250px] xs:h-[250px] sm:h-[300px] md:h-[400px] lg:h-[500px] -mx-2 xs:-mx-3 sm:-mx-4 md:-mx-6 lg:-mx-8 xl:-mx-16 flex items-center justify-center overflow-hidden"
              style={{
                y: y1,
                marginTop: "0.5rem",
                marginBottom: "1rem"
              }}
              initial={{ opacity: 0, scale: 0.8 }}
              animate={{ opacity: isInView ? 1 : 0, scale: isInView ? 1 : 0.8 }}
              transition={{ duration: 1.5, delay: 1.5 }}
            >
              {/* Main 3D scene container - Much larger */}
              <motion.div
                className="relative w-[280px] xs:w-[320px] sm:w-[400px] md:w-[450px] lg:w-[500px] h-[200px] xs:h-[250px] sm:h-[300px] md:h-[400px] lg:h-[500px]"
                style={{
                  transformStyle: "preserve-3d",
                  perspective: "1400px"
                }}
              >
                {/* Floating Monitor - Larger */}
                <motion.div
                  className="absolute inset-0"
                  animate={{
                    rotateY: [0, 15, -15, 0],
                    y: [0, -15, 0, 15, 0]
                  }}
                  transition={{
                    duration: 12,
                    repeat: Infinity,
                    ease: "easeInOut"
                  }}
                  style={{ transformStyle: "preserve-3d" }}
                >
                  {/* Monitor Screen - Larger */}
                  <motion.div
                    className="absolute top-[10%] left-[10%] right-[10%] bottom-[25%] bg-gradient-to-br from-gray-900 to-black rounded-2xl xs:rounded-3xl border-2 xs:border-4 border-secondary/40 overflow-hidden shadow-2xl"
                    style={{ transform: "translateZ(30px)" }}
                  >
                    {/* Code lines on screen - More content */}
                    <div className="p-2 xs:p-3 sm:p-4 md:p-6 font-mono text-[10px] xs:text-xs sm:text-sm space-y-1 xs:space-y-2">
                      <motion.div
                        className="text-green-400"
                        animate={{ opacity: [0.5, 1, 0.5] }}
                        transition={{ duration: 2, repeat: Infinity }}
                      >
                        {"const project = {"}
                      </motion.div>
                      <motion.div
                        className="text-blue-400 ml-6"
                        animate={{ opacity: [0.5, 1, 0.5] }}
                        transition={{ duration: 2, repeat: Infinity, delay: 0.3 }}
                      >
                        {"name: 'fordigi-web',"}
                      </motion.div>
                      <motion.div
                        className="text-purple-400 ml-6"
                        animate={{ opacity: [0.5, 1, 0.5] }}
                        transition={{ duration: 2, repeat: Infinity, delay: 0.6 }}
                      >
                        {"status: 'building...',"}
                      </motion.div>
                      <motion.div
                        className="text-yellow-400 ml-6"
                        animate={{ opacity: [0.5, 1, 0.5] }}
                        transition={{ duration: 2, repeat: Infinity, delay: 0.9 }}
                      >
                        {"framework: 'Next.js',"}
                      </motion.div>
                      <motion.div
                        className="text-cyan-400 ml-6"
                        animate={{ opacity: [0.5, 1, 0.5] }}
                        transition={{ duration: 2, repeat: Infinity, delay: 1.2 }}
                      >
                        {"team: 'fordigi',"}
                      </motion.div>
                      <motion.div
                        className="text-pink-400 ml-6"
                        animate={{ opacity: [0.5, 1, 0.5] }}
                        transition={{ duration: 2, repeat: Infinity, delay: 1.5 }}
                      >
                        {"quality: 'premium'"}
                      </motion.div>
                      <motion.div
                        className="text-green-400"
                        animate={{ opacity: [0.5, 1, 0.5] }}
                        transition={{ duration: 2, repeat: Infinity, delay: 1.8 }}
                      >
                        {"};"}
                      </motion.div>
                    </div>

                    {/* Blinking cursor */}
                    <motion.div
                      className="absolute bottom-2 xs:bottom-4 sm:bottom-6 left-2 xs:left-4 sm:left-6 w-2 xs:w-3 h-3 xs:h-5 bg-green-400"
                      animate={{ opacity: [1, 0, 1] }}
                      transition={{ duration: 1, repeat: Infinity }}
                    />
                  </motion.div>
                </motion.div>

                {/* Floating Keyboard - Adjusted positioning */}
                <motion.div
                  className="absolute bottom-[5%] left-1/2 transform -translate-x-1/2"
                  animate={{
                    rotateX: [0, 5, -5, 0],
                    y: [20, 10, 20],
                    rotateY: [0, -8, 8, 0]
                  }}
                  transition={{
                    duration: 8,
                    repeat: Infinity,
                    ease: "easeInOut",
                    delay: 1
                  }}
                  style={{
                    transformStyle: "preserve-3d",
                    transform: "translateX(-50%) translateZ(-25px)"
                  }}
                >
                  {/* Keyboard Body - Adjusted size */}
                  <motion.div
                    className="w-[180px] xs:w-[200px] sm:w-[250px] md:w-[300px] lg:w-[320px] h-[40px] xs:h-[15px] sm:h-[18px] md:h-[60px] bg-gradient-to-br from-gray-800 to-gray-900 rounded-lg xl:rounded-xl border border-secondary/30 relative overflow-hidden shadow-lg"
                    style={{
                      transform: "translateZ(0px)",
                      boxShadow: "0 10px 25px rgba(15, 60, 120, 0.15), 0 4px 10px rgba(0, 0, 0, 0.1)"
                    }}
                  >
                    {/* Keyboard Keys Grid - Adjusted grid */}
                    <div className="grid grid-cols-12 gap-0.5 xs:gap-1 p-1 xs:p-2 h-full">
                      {[...Array(48)].map((_, i) => (
                        <motion.div
                          key={i}
                          className="bg-gradient-to-b from-gray-600 to-gray-700 rounded-[1px] sm:rounded-sm border border-gray-500/50"
                          animate={{
                            scale: [1, 0.95, 1],
                            backgroundColor: Math.random() > 0.85 ? ["rgb(75, 85, 99)", "rgb(15, 60, 120)", "rgb(75, 85, 99)"] : "rgb(75, 85, 99)"
                          }}
                          transition={{
                            duration: Math.random() * 2 + 1,
                            repeat: Infinity,
                            delay: Math.random() * 4
                          }}
                        />
                      ))}
                    </div>
                  </motion.div>
                </motion.div>

                {/* Binary Numbers Floating in 3D scene - Adjusted for mobile */}
                {binary3DPositions.slice(0, window.innerWidth < 640 ? 15 : 30).map((pos, i) => (
                  <motion.div
                    key={`3d-bit-${i}`}
                    className="absolute text-secondary/80 font-mono text-xs xs:text-sm sm:text-base font-bold"
                    style={{
                      left: `${pos.left * (window.innerWidth < 640 ? 0.5 : 1)}px`,
                      top: `${pos.top * (window.innerWidth < 640 ? 0.5 : 1)}px`,
                      transform: `translateZ(${pos.z * (window.innerWidth < 640 ? 0.5 : 1)}px)`
                    }}
                    animate={{
                      y: [0, -15, 0],
                      opacity: [0.4, 0.8, 0.4],
                      scale: [0.8, 1.2, 0.8]
                    }}
                    transition={{
                      duration: pos.duration,
                      repeat: Infinity,
                      delay: pos.delay,
                      ease: "easeInOut"
                    }}
                  >
                    {pos.bit}
                  </motion.div>
                ))}
              </motion.div>
            </motion.div>


          </motion.div>
        </div>
      </motion.div>


    </section>
  );
};

// Legacy components for backwards compatibility
export const BentoGrid = ({
  className,
  children,
}: {
  className?: string;
  children?: React.ReactNode;
}) => {
  return (
    <div
      className={cn(
        "mx-auto grid max-w-7xl grid-cols-1 gap-4 md:auto-rows-[18rem] md:grid-cols-3",
        className,
      )}
    >
      {children}
    </div>
  );
};

export const BentoGridItem = ({
  className,
  title,
  description,
  header,
  icon,
}: {
  className?: string;
  title?: string | React.ReactNode;
  description?: string | React.ReactNode;
  header?: React.ReactNode;
  icon?: React.ReactNode;
}) => {
  return (
    <div
      className={cn(
        "group/bento shadow-input row-span-1 flex flex-col justify-between space-y-4 rounded-xl border border-neutral-200 bg-white p-4 transition duration-200 hover:shadow-xl dark:border-white/[0.2] dark:bg-black dark:shadow-none",
        className,
      )}
    >
      {header}
      <div className="transition duration-200 group-hover/bento:translate-x-2">
        {icon}
        <div className="mt-2 mb-2 font-sans font-bold text-neutral-600 dark:text-neutral-200">
          {title}
        </div>
        <div className="font-sans text-xs font-normal text-neutral-600 dark:text-neutral-300">
          {description}
        </div>
      </div>
    </div>
  );
};
