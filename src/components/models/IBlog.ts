import { IBlockNode } from "./BlockNode.js";


export interface IBlog {
    _id: string;
    title: string;
    description: string;
    tags: string[];
    createdAt: string;
    updatedAt: string;
    author?: {
        _id: string;
        name: string;
        avatar?: string;
    },
    publish: boolean;
    content?: {
        styles: string;
        blocks: IBlockNode[] | IBlockNode;
    }
}