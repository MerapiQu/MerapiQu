import { Response } from "./Response";

export interface NavigationResponse extends Response<NavigationPayloads> {}

export interface NavigationPayloads {
  title: string;
  description: string;
  module: string;
}

export interface NavigationProps {}
