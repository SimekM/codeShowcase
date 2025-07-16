import React from 'react';
import Link from 'next/link';
import Image from 'next/image';

export const Footer = () => {
    return (
        <footer id="footer" className="py-12 px-4 mb-8">
            <div className="max-w-5xl mx-auto bg-white/95 backdrop-blur-md rounded-2xl shadow-[0_8px_30px_rgba(0,0,0,0.08)] px-8 py-10 border border-gray-100/50 relative overflow-hidden">
                {/* Background decorative elements */}
                <div className="absolute top-20 right-[10%] w-64 h-64 rounded-full bg-secondary/5 blur-3xl"></div>
                <div className="absolute bottom-20 left-[5%] w-96 h-96 rounded-full bg-secondary/5 blur-3xl"></div>
                
                <div className="grid grid-cols-12 gap-8 relative">
                    {/* Logo and company info */}
                    <div className="col-span-12 md:col-span-5">
                        <div className="mb-6">
                            <Link href="/" className="flex items-center group">
                                <Image 
                                    src="/logo-light.png" 
                                    alt="Fordigi Logo" 
                                    width={120} 
                                    height={40}
                                    className="transition-all duration-500 ease-out"
                                />
                            </Link>
                        </div>
                        <p className="text-sm text-text/70 mb-4 leading-relaxed">
                            Rychlý a snadný způsob, jak vytvořit působivé
                            digitální produkty a zrychlit váš vývojový cyklus.
                        </p>
                        <p className="text-sm text-text/70">
                            Vytvořeno s ♥ v Česku
                        </p>
                    </div>

                    {/* Links from Navbar */}
                    <div className="col-span-12 md:col-span-3 md:col-start-7">
                        <h3 className="text-base font-medium text-secondary mb-4">Odkazy</h3>
                        <ul className="space-y-2.5">
                            <li>
                                <Link href="/solutions" className="text-sm text-text/70 hover:text-secondary transition-colors">
                                    Služby
                                </Link>
                            </li>
                            <li>
                                <Link href="/features" className="text-sm text-text/70 hover:text-secondary transition-colors">
                                    O nás
                                </Link>
                            </li>
                            <li>
                                <Link href="/pricing" className="text-sm text-text/70 hover:text-secondary transition-colors">
                                    Kontakt
                                </Link>
                            </li>
                        </ul>
                    </div>

                    {/* Contact info */}
                    <div className="col-span-12 md:col-span-3 md:col-start-10 flex flex-col md:items-end">
                        <div className="">
                            <h3 className="text-base font-medium text-secondary mb-4">Kontakt</h3>
                            <ul className="space-y-3">
                                <li className="flex items-start group">
                                    <svg className="h-5 w-5 text-secondary mr-2 mt-0.5 flex-shrink-0 transition-transform group-hover:scale-110" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
                                        <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                                        <polyline points="22,6 12,13 2,6"></polyline>
                                    </svg>
                                    <span className="text-sm text-text/70 hover:text-secondary transition-colors break-words">info@fordigi.com</span>
                                </li>
                                {/* <li className="flex items-start group">
                                    <svg className="h-5 w-5 text-secondary mr-2 mt-0.5 flex-shrink-0 transition-transform group-hover:scale-110" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
                                        <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path>
                                    </svg>
                                    <span className="text-sm text-text/70 hover:text-secondary transition-colors">+420 603 179 313</span>
                                </li> */}
                                <li className="flex items-start group">
                                    <svg className="h-5 w-5 text-secondary mr-2 mt-0.5 flex-shrink-0 transition-transform group-hover:scale-110" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
                                        <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                                        <circle cx="12" cy="10" r="3"></circle>
                                    </svg>
                                    <span className="text-sm text-text/70 hover:text-secondary transition-colors">Pujmanové <br/>1753/10, Prague 4</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                {/* Copyright */}
                <div className="mt-10 pt-8 border-t border-gray-100/50">
                    <div className="flex flex-col md:flex-row justify-between items-center">
                        <p className="text-sm text-text/50">
                            &copy; {new Date().getFullYear()} Fordigi. Všechna práva vyhrazena.
                        </p>
                        <div className="flex space-x-6 mt-4 md:mt-0">
                            <Link href="#" className="text-sm text-text/50 hover:text-secondary transition-colors">
                                Podmínky použití
                            </Link>
                            <Link href="#" className="text-sm text-text/50 hover:text-secondary transition-colors">
                                Ochrana soukromí
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </footer>
    );
};
