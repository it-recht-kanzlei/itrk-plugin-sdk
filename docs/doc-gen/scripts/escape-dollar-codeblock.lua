function CodeBlock(el)
  -- Replace $ with $\$$ in codeblocks
  el.text = el.text:gsub("%$", "$\\$$")
  return el
end
