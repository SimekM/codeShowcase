import { ReactNode } from 'react';

export interface ServiceContent {
  title: string;
  description: string;
  link: string;
  image: string;
  bgGradient: string;
  icon: ReactNode;
}

export interface ServicesGridProps {
  content: ServiceContent[];
} 