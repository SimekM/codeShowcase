import type { Metadata } from "next";
import { Raleway, Outfit} from "next/font/google";
import "./utils/globals.css";
import { Navbar } from "./components/ui/Navbar";
import { Footer } from "./components/ui/footer";

const ralewayFont = Raleway({
  variable: "--font-raleway",
  subsets: ["latin"],
});

const outfitFont = Outfit({
  variable: "--font-outfit",
  subsets: ["latin"],
});

export const metadata: Metadata = {
  title: "Fordigi | Digitální Agentura", 
  description: "Modern digital solutions for your business",
};

export default function RootLayout({
  children,
}: Readonly<{
  children: React.ReactNode;
}>) {
  return (
    <html lang="en">
      <body
        className={`${ralewayFont.variable}, ${ralewayFont.variable}, ${outfitFont.variable} antialiased`}
      >
        <Navbar />
        {children}
        <Footer />
      </body>
    </html>
  );
}
