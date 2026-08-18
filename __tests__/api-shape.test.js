import { describe, expect, it } from "vitest";
import { getApiItems, getApiPage, getApiResource } from "../src/lib/api";

describe("API response helpers", () => {
  it("normalizes flat and resource collection responses", () => {
    const items = [{ id: 1 }, { id: 2 }];

    expect(getApiItems(items)).toEqual(items);
    expect(getApiItems({ data: items })).toEqual(items);
    expect(getApiItems({ data: { data: items } })).toEqual(items);
  });

  it("normalizes paginated responses and derives hasMore when needed", () => {
    const response = {
      data: { data: [{ id: 1 }], current_page: 2, last_page: 3 },
    };

    expect(getApiPage(response)).toEqual({
      items: [{ id: 1 }],
      currentPage: 2,
      lastPage: 3,
      hasMore: true,
    });
  });

  it("does not unwrap arrays as a resource", () => {
    const resource = { id: 7, title: "Serv5" };

    expect(getApiResource({ data: resource })).toEqual(resource);
    expect(getApiResource(resource)).toEqual(resource);
  });
});
