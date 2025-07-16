"use client";

import { useState, useEffect } from "react";
import Link from "next/link";
import Image from "next/image";
import { motion, AnimatePresence } from "motion/react";

export const Navbar = () => {
  const [scrolled, setScrolled] = useState(false);
  const [activeLink, setActiveLink] = useState("");
  const [mobileMenuOpen, setMobileMenuOpen] = useState(false);

  useEffect(() => {
    const handleScroll = () => {
      const isScrolled = window.scrollY > 20;
      if (isScrolled !== scrolled) {
        setScrolled(isScrolled);
      }
    };

    window.addEventListener("scroll", handleScroll, { passive: true });
    return () => window.removeEventListener("scroll", handleScroll);
  }, [scrolled]);

  const navItems = [
    { name: "Služby", path: "#sluzby" },
    { name: "O nás", path: "#onas" },
    { name: "Kontakt", path: "#footer" },
  ];

  // Smooth scroll handler
  const handleNavClick = (e: React.MouseEvent<HTMLAnchorElement>, id: string) => {
    e.preventDefault();
    const el = document.getElementById(id);
    if (el) {
      el.scrollIntoView({ behavior: "smooth" });
    }
    setMobileMenuOpen(false);
    setActiveLink(id);
  };

  return (
    <>
      <motion.header
        className={`fixed top-0 left-0 right-0 z-[100] flex justify-center px-4 sm:px-6 mt-5 md:mt-6 transition-all duration-300 ease-in-out`}
        initial={{ opacity: 0, y: -20 }}
        animate={{ opacity: 1, y: 0 }}
        transition={{ duration: 0.5 }}
      >
        <div
          className={`
            relative w-full 
            flex items-center justify-between
            ${scrolled
              ? "max-w-5xl py-2.5 px-4 sm:px-6"
              : "max-w-6xl py-3.5 px-5 sm:py-4 sm:px-6"
            }
            transition-all duration-400
            rounded-2xl
            ${scrolled
              ? "bg-white/90 backdrop-blur-lg shadow-[0_8px_30px_rgba(0,0,0,0.08)]"
              : "bg-white/95 backdrop-blur-md shadow-[0_10px_40px_rgba(0,0,0,0.06)]"
            }
            before:content-[''] before:absolute before:inset-0 before:z-[-1] before:rounded-2xl
            before:bg-gradient-to-r before:from-indigo-500/10 before:via-transparent before:to-purple-500/10
            before:opacity-0 hover:before:opacity-100 before:transition-opacity before:duration-700
          `}
        >
          {/* Logo */}
          <Link href="/" className="flex items-center group z-10">
            <motion.div
              className="relative overflow-hidden"
              whileHover={{ scale: 1.03 }}
              whileTap={{ scale: 0.98 }}
              transition={{ type: "spring", stiffness: 400, damping: 10 }}
            >
              <Image
                src="/logo-light.png"
                alt="Agency Logo"
                width={120}
                height={40}
                className={`transition-all duration-500 ease-out ${scrolled ? "scale-[0.93]" : ""}`}
                priority
              />
              <motion.div
                className="absolute bottom-0 left-0 h-[2px] w-0 bg-gradient-to-r from-blue-600 via-indigo-500 to-purple-500"
                initial={{ width: 0 }}
                whileHover={{ width: "100%" }}
                transition={{ duration: 0.3 }}
              />
            </motion.div>
          </Link>

          {/* Navigation Links - desktop only */}
          <div className="hidden md:flex items-center justify-center z-10">
            <nav className="flex items-center gap-1">
              {navItems.map((item) => (
                <motion.div
                  key={item.name}
                  whileHover={{ scale: 1.05 }}
                  whileTap={{ scale: 0.95 }}
                  className="relative mx-1"
                >
                  <Link
                    href={item.path}
                    className={`relative px-4 py-2 font-medium text-md tracking-wide transition-all duration-300 rounded-lg overflow-hidden ${activeLink === item.name
                        ? "text-text/90"
                        : "text-text/90 hover:text-text"
                      }`}
                    onClick={e => handleNavClick(e, item.path.replace('#', ''))}
                  >
                    <span className="relative z-10">{item.name}</span>
                    {activeLink === item.name && (
                      <motion.div
                        className="absolute inset-0 bg-gradient-to-r from-secondary/10 to-indigo-500/10 rounded-lg -z-0"
                        layoutId="navBackground"
                        initial={{ opacity: 0 }}
                        animate={{ opacity: 1 }}
                        transition={{ duration: 0.3 }}
                      />
                    )}
                  </Link>
                </motion.div>
              ))}
            </nav>
          </div>

          {/* Contact Button - desktop only */}
          <motion.div
            className="relative z-10 ml-2 hidden md:inline-flex"
            whileHover={{ scale: 1.05 }}
            whileTap={{ scale: 0.95 }}
          >
            <Link
              href="/contact"
              className="inline-flex items-center justify-center px-5 py-2 text-sm font-medium text-white bg-gradient-to-r from-secondary to-secondary rounded-2xl shadow-lg shadow-blue-500/20 hover:shadow-xl hover:shadow-blue-500/30 transition-all duration-300 relative overflow-hidden group"
              onClick={() => setMobileMenuOpen(false)}
            >
              <span className="relative z-10 tracking-wide">Kontaktujte nás</span>
              <span className="absolute top-0 left-0 w-full h-full bg-gradient-to-r from-transparent via-white/20 to-transparent -translate-x-[150%] group-hover:translate-x-[150%] transition-transform duration-700 ease-in-out" />
            </Link>
          </motion.div>

          {/* Mobile Menu Button */}
          <div className="md:hidden z-10">
            <motion.button
              className="flex items-center justify-center w-10 h-10 rounded-lg bg-gray-50/80 backdrop-blur-sm shadow-sm text-gray-700 hover:text-blue-600 transition-colors"
              onClick={() => setMobileMenuOpen(!mobileMenuOpen)}
              whileTap={{ scale: 0.9 }}
              aria-label="Toggle mobile menu"
            >
              <div className="relative w-5 h-5">
                <motion.span
                  className="absolute left-0 top-0 w-5 h-0.5 bg-current rounded-full"
                  animate={{ rotate: mobileMenuOpen ? 45 : 0, y: mobileMenuOpen ? 8 : 0 }}
                  transition={{ type: "spring", stiffness: 260, damping: 20 }}
                />
                <motion.span
                  className="absolute left-0 top-2 w-5 h-0.5 bg-current rounded-full"
                  animate={{ opacity: mobileMenuOpen ? 0 : 1, x: mobileMenuOpen ? -10 : 0 }}
                  transition={{ duration: 0.2 }}
                />
                <motion.span
                  className="absolute left-0 top-4 w-5 h-0.5 bg-current rounded-full"
                  animate={{ rotate: mobileMenuOpen ? -45 : 0, y: mobileMenuOpen ? -8 : 0 }}
                  transition={{ type: "spring", stiffness: 260, damping: 20 }}
                />
              </div>
            </motion.button>
          </div>
        </div>

        {/* Floating glow decoration */}
        <div className="absolute -z-10 top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-full h-full max-w-5xl pointer-events-none">
          <div className="absolute left-10 top-1/2 w-40 h-40 bg-purple-500/20 rounded-full filter blur-3xl opacity-30 animate-pulse" style={{ animationDuration: '8s', animationDelay: '0.5s' }} />
          <div className="absolute right-10 top-1/2 w-32 h-32 bg-blue-500/20 rounded-full filter blur-3xl opacity-30 animate-pulse" style={{ animationDuration: '10s', animationDelay: '1s' }} />
        </div>
      </motion.header>

      {/* Mobile menu overlay */}
      <AnimatePresence>
        {mobileMenuOpen && (
          <motion.div
            className="fixed inset-0 bg-black/10 backdrop-blur-sm z-[90] flex md:hidden"
            initial={{ opacity: 0 }}
            animate={{ opacity: 1 }}
            exit={{ opacity: 0 }}
            transition={{ duration: 0.3 }}
            onClick={() => setMobileMenuOpen(false)}
          >
            <motion.div
              className="relative max-w-sm w-[85%] ml-auto h-full bg-white flex flex-col"
              initial={{ x: "100%" }}
              animate={{ x: 0 }}
              exit={{ x: "100%" }}
              transition={{ type: "spring", damping: 25, stiffness: 200 }}
              onClick={(e) => e.stopPropagation()}
            >
              <div className="flex justify-between items-center p-5 border-b border-gray-100">
                <Image
                  src="/logo-light.png"
                  alt="Agency Logo"
                  width={100}
                  height={40}
                  priority
                />
                <motion.button
                  className="w-8 h-8 flex items-center justify-center rounded-full bg-gray-100 text-gray-700"
                  onClick={() => setMobileMenuOpen(false)}
                  whileTap={{ scale: 0.9 }}
                >
                  <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                  </svg>
                </motion.button>
              </div>

              <nav className="flex flex-col px-5 py-6">
                {navItems.map((item, index) => (
                  <motion.div
                    key={item.name}
                    initial={{ opacity: 0, x: 20 }}
                    animate={{ opacity: 1, x: 0 }}
                    exit={{ opacity: 0, x: 20 }}
                    transition={{ delay: index * 0.1, duration: 0.3 }}
                  >
                    <Link
                      href={item.path}
                      className={`px-4 py-3.5 mb-2 rounded-xl font-medium text-base transition-all flex items-center ${activeLink === item.name
                          ? "bg-gradient-to-r from-blue-50 to-indigo-50 text-blue-600"
                          : "text-gray-700 hover:bg-gray-50"
                        }`}
                      onClick={e => {
                        handleNavClick(e, item.path.replace('#', ''));
                      }}
                    >
                      {item.name}
                      {activeLink === item.name && (
                        <motion.span
                          className="ml-auto w-1 h-5 bg-blue-500 rounded-full"
                          layoutId="mobileActiveIndicator"
                        />
                      )}
                    </Link>
                  </motion.div>
                ))}
              </nav>

              <div className="mt-auto px-5 pb-8 pt-4 border-t border-gray-100">
                <Link
                  href="/contact"
                  className="w-full flex items-center justify-center gap-2 px-4 py-3 text-white bg-gradient-to-r from-blue-600 to-indigo-500 rounded-xl font-medium shadow-lg shadow-blue-500/10 hover:shadow-blue-500/20 transition-all"
                  onClick={() => setMobileMenuOpen(false)}
                >
                  <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
                    <path d="M14 9a2 2 0 0 1-2 2H6l-4 4V4c0-1.1.9-2 2-2h8a2 2 0 0 1 2 2v5Z" />
                    <path d="M18 9h2a2 2 0 0 1 2 2v11l-4-4h-6a2 2 0 0 1-2-2v-1" />
                  </svg>
                  <span>Kontaktujte nás</span>
                </Link>
              </div>
            </motion.div>
          </motion.div>
        )}
      </AnimatePresence>
    </>
  );
}; 