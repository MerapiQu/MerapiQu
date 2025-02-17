export interface BlockProps {
    tagName?: string; 
    type?: string;
    attribute?: {
      [key: string]: string; 
    };
    children?: string | string[] | BlockProps[];
    [key: string]: any; 
  }