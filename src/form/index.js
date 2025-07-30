import { registerBlockType } from "@wordpress/blocks";

import "./style.css";

import Edit from "./edit";
import metadata from "./block.json";

const Icon = () => (
  <svg
    xmlns="http://www.w3.org/2000/svg"
    width="32"
    height="32"
    viewBox="0 0 48 48"
  >
    <path
      fill="none"
      stroke="currentColor"
      stroke-linecap="round"
      stroke-linejoin="round"
      d="M26.755 39.536L6.31 26.306c-2.236-1.702-2.498-6.702-.239-8.914L19.87 8.465l23.63 15.29l-12.067 7.807a4.78 4.78 0 0 0-2.41 4.207c-.008 1.467.848 3.439 2.41 3.038L43.5 30.999l-5.325-3.445"
    />
    <path
      fill="none"
      stroke="currentColor"
      stroke-linecap="round"
      stroke-linejoin="round"
      d="m19.87 8.465l1.351 13.428L43.5 23.755"
    />
  </svg>
);

registerBlockType(metadata.name, {
  edit: Edit,
  icon: Icon,
});
