import { ServicesSticky } from "./HomeServices";
import { ServicesGridProps } from "./types";

export default function ClientServicesSticky({ content }: ServicesGridProps) {
    return <ServicesSticky content={content} />;
} 